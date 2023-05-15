import $ from "jquery";
import whatInput from "what-input";
import Foundation from "foundation-sites";
Foundation.Abide.defaults.patterns["zip_code"] = /^\d{5}(?:[-]\d{4})?$/;
Foundation.Abide.defaults.patterns["cii_password"] = /\S/;

import "../modules/header";
//import Foundation from 'foundation-sites';
// If you want to pick and choose which modules to include, comment out the above and uncomment
// the line below
import "../lib/foundation-explicit-pieces";
import Flickity from "flickity";
import "flickity-as-nav-for";
import jQueryBridget from "jquery-bridget";

import "../util/elementIsInView";
import "../util/jqInitialize";
import "../util/doTimeout";
import "../util/lazyLoadImgs";
import "../components/button";
import "../components/layout";
import "../components/embedded-video-thumbnail";

import "../components/lists";
import "../components/forms";
import "../components/modals";
import "../components/links";
import "../components/cards";
import "../components/menus";
import "../components/password";
import "../components/reveal-form";

import "../templates/blog";

import "../components/footer";
//import '../components/donate';
//import '../components/events';
import "../components/style-raisers-edge-forms";
import "../components/go-to-anchor";
import "../components/resource-filters";

// If you want to pick and choose which modules to include, comment out the above and uncomment
// the line below
//import './lib/foundation-explicit-pieces';

//import module functionality
import "../modules/single-row-cta";
import "../modules/card-list";
import "../modules/image-carousel";
import "../modules/past-upcoming-event-card-list";
import "../modules/calendar-list";
import "../modules/location-card-list";
import "../modules/contact-form";
import "../modules/homepage-header";
import "../modules/people-list";

export default {
	init() {
		// JavaScript to be fired on all pages
		window.$ = $;
		$(document).foundation();

		// make Flickity a jQuery plugin
		Flickity.setJQuery($);
		jQueryBridget("flickity", Flickity, $);
	},
	finalize() {
		// JavaScript to be fired on all pages, after page specific JS is fired
	},
};
