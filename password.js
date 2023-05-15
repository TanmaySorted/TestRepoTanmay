(function ($) {
	//Event listener to password field
	$('.post-password-form-container input[type="password"]').on(
		"keypress",
		function (e) {
			if (e.which === 32) {
				// Check if the key pressed is space
				e.preventDefault();
			}
		}
	);
})(jQuery);
