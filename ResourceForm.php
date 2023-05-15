<?php

namespace AppControllers;

if (!class_exists('ResourceForm')) :
    class ResourceForm
    {

        public function __construct()
        {
            add_filter('wpcf7_form_tag', [$this, 'AttachResourceTaxonomy'], 10);
            add_filter('wpcf7_validate_textarea', [$this, 'ValidateContactInfo'], 20, 2);
            add_action('wpcf7_before_send_mail', [$this, 'CreateResourcePost']);
            add_filter('the_password_form', [$this, 'CustomPostPasswordForm']);
        }

        /**
         * Dynamic Select List for Contact Form 7
         * @usage [select name taxonomy:{$taxonomy} ...]
         * 
         * @param Array $tag
         * 
         * @return Array $tag
         */
        public static function AttachResourceTaxonomy($tag)
        {
            // Only run on select lists
            if ('resource_category' !== $tag['name'] && ('partner_tier' !== $tag['name'])) {
                return $tag;
            }

            // Merge dynamic arguments with static arguments
            $term_args = [
                'taxonomy' => $tag['name'],
                'hide_empty' => false
            ];

            $terms = get_terms($term_args);

            foreach ($terms as $term) {
                $tag['raw_values'][] = $term->term_id . '|' . $term->name;
            }

            $pipes = new \WPCF7_Pipes($tag['raw_values']);

            $tag['values'] = $pipes->collect_befores();
            $tag['labels'] = $pipes->collect_afters();
            $tag['pipes'] = $pipes;

            return $tag;
        }

        public static function ValidateContactInfo($result, $tag)
        {
            $resource_form_id = get_field('resource_form', 'option') ?: 0;
            if (isset($_POST['_wpcf7']) && (int)$_POST['_wpcf7'] !== (int)$resource_form_id) {
                return $result;
            }

            if ('street' !== $tag->name) {
                return $result;
            }

            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $email = isset($_POST['resource_email']) ? trim($_POST['resource_email']) : '';
            $street = isset($_POST['street']) ? trim($_POST['street']) : '';

            if ($phone === '' && $email === '' && $street === '') {
                $result->invalidate($tag, "Atleast one contact information (Phone/ Email/ Street) is required.");
            }

            return $result;
        }


        public static function CreateResourcePost($contact_form)
        {
            $form_id = $contact_form->id();
            $resource_form_id = get_field('resource_form', 'option') ?: 0;

            // Exit if not resource form
            if ($form_id !== $resource_form_id) {
                return;
            }

            $submission = \WPCF7_Submission::get_instance();

            if ($submission) {
                $posted_data = $submission->get_posted_data();

                // Create a new post
                $new_post = array(
                    'post_title'    => $posted_data['resource_name'],
                    'post_type'     => 'resources',
                    'post_status'   => 'publish',
                    'meta_input' => [
                        'cii_name' => $posted_data['cii_name'],
                        'cii_email' => $posted_data['cii_email'],
                        'description' => $posted_data['description'],
                        'services' => $posted_data['services'],
                        'phone' => $posted_data['phone'],
                        'resource_email' => $posted_data['resource_email'],
                        'external_link' => $posted_data['external_link'],
                        'street' => $posted_data['street'],
                        'city' => $posted_data['city'],
                        'state' => $posted_data['state'],
                        'country' => $posted_data['country'],
                        'zip_code' => $posted_data['zip_code']
                    ]
                );

                // Insert the post into the database
                $post_id = wp_insert_post($new_post);

                if (!empty($posted_data['resource_category'])) {
                    wp_set_object_terms($post_id, $posted_data['resource_category'], 'resource_category');
                }

                if (!empty($posted_data['partner_tier'])) {
                    wp_set_object_terms($post_id, $posted_data['partner_tier'], 'partner_tier');
                }


                // Save the repeater field data as post meta
                // Get the values of the repeater field
                $services = array();
                for ($i = 1; $i <= $posted_data['uarepeater-285_count']; $i++) {
                    $service_name = isset($posted_data['services__' . $i]) ? $posted_data['services__' . $i] : '';
                    if ($service_name) {
                        $services[] = array(
                            'service' => $service_name,
                        );
                    }
                }
                update_field('services_list', $services, $post_id);
            }
        }


        /**
         * Add a message to the password form.
         *
         * @wp-hook the_password_form
         * @param   string $form
         * @return  string
         */
        public static function CustomPostPasswordForm($passwordForm)
        {

            global $post;
            $label = 'pwbox-' . (empty($post->ID) ? rand() : $post->ID);
            $form =  '<form action="' . esc_url(site_url('wp-login.php?action=postpass', 'login_post')) . '" class="post-password-form" method="post" data-abide novalidate>
            <div class="form-info">' . esc_html__('This content is password protected. To view it please enter your password below', 'text-domain') . '</div>
            <label class="pass-label" for="' . $label . '">' . esc_html__('PASSWORD:', 'text-domain') . ' </label>
            <div class="field-container"><input name="post_password" id="' . $label . '" type="password"  required pattern="cii_password"/>
            <span class="form-error" data-form-error-for="' . $label . '" >Please enter valid password..</span>
            
            </div>
            <input type="submit" name="Submit" class="button cii-btn-primary cii-submit" value="' . esc_attr__('Submit', 'text-domain') . '" />
            <span class="form-error hide" data-form-error-for="' . $label . '" >Please enter valid password..</span>
            </form>';

            // Translate and escape.
            $msg = isset($_COOKIE['wp-postpass_' . COOKIEHASH]) ? '<div class="wrong-password-message">
                    The password you entered is incorrect. Please try again.
                </div>' : '';


            return '<div class="post-password-form-container">' . $msg . $form . '</div>';
        }
    }

    /**
     * Initialize class
     */
    global $ResourceForm;
    $ResourceForm = new \AppControllers\ResourceForm();
endif;
