/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

define([
	'jquery',
	'jquery.ui'
],

function ($)
{
	var JSNBanner = function(params)
	{
		// Object parameters
		this.params = $.extend({campaign: ''}, params);
		this.lang = this.params.language || {};

		// Do initialization
		$(document).ready($.proxy(function() {
			this.initialize();
		}, this));
	};

	JSNBanner.prototype = {
		initialize: function() {
			// Handle click event on the button to close a banner.
			$('.jsn-footer-banner > .close-banner').click(function(event) {
				event.preventDefault();

				// Create modal backdrop.
				this.overlay = document.createElement('div');
				this.overlay.className = 'ui-widget-overlay';

				this.overlay.style.position = 'fixed';
				this.overlay.style.zIndex = '9999';

				document.body.appendChild(this.overlay);

				// Create modal.
				this.container = document.createElement('div');
				this.container.className = 'ui-dialog ui-widget ui-widget-content ui-corner-all jsn-master ui-dialog-buttons';

				this.container.style.position = 'fixed';
				this.container.style.top = '50%';
				this.container.style.left = '50%';
				this.container.style.width = '720px';
				this.container.style.maxWidth = '90%';
				this.container.style.maxHeight = '90%';
				this.container.style.zIndex = '9999';

				this.container.innerHTML = '<div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">'
					+ '<span class="ui-dialog-title">' + this.lang.JSN_EXTFW_BANNER_POPUP_MODAL_HEADER_TEXT + '</span>'
					+ '<a href="#" class="ui-dialog-titlebar-close ui-corner-all" style="display: initial;">'
					+ '<span class="ui-icon ui-icon-closethick">close</span></a></div>'
					+ '<div class="ui-dialog-content ui-widget-content">'
					+ '<div class="container-fluid">'
					+ '<h3>' + this.lang.JSN_EXTFW_BANNER_POPUP_MODAL_BODY_TITLE + '</h3>'
					+ '<p>' + this.lang.JSN_EXTFW_BANNER_POPUP_MODAL_BODY_MESSAGE + '</p></div></div>'
					+ '<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">'
					+ '<div class="ui-dialog-buttonset">'
					+ '<a href="' + this.params.purchase + '" target="_blank" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" rel="noopener noreferrer">'
					+ '<span class="ui-button-text">' + this.lang.JSN_EXTFW_BANNER_POPUP_MODAL_FOOTER_BUTTON_TEXT + '</span>'
					+ '</a></div></div>';

				document.body.appendChild(this.container);

				this.container.style.marginTop = '-' + (this.container.offsetHeight / 2) + 'px';
				this.container.style.marginLeft = '-' + (this.container.offsetWidth / 2) + 'px';

				// Handle click event to close the modal.
				var close = this.container.querySelector('.ui-dialog-titlebar-close');

				close.addEventListener('click', function(event) {
					event.preventDefault();

					// Remove the modal.
					this.container.parentNode.removeChild(this.container);

					// Remove the modal backdrop.
					this.overlay.parentNode.removeChild(this.overlay);
				}.bind(this));

				return false;
			}.bind(this));
		}
	};

	return JSNBanner;
});
