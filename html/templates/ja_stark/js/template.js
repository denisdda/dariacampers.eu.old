// Add Inview
jQuery(document).ready(function () {
  jQuery('.t4-section-inview').bind('inview', function(event, visible) {
    if (visible) {
      jQuery(this).addClass('t4-inview');
      var animateClass = jQuery(this).find('.animated').data('animated-type');
      jQuery(this).find('.animated').addClass(animateClass);
    }
  });
});