Drupal.behaviors.styles_widget = {
  attach: function (context, settings) {
    jQuery('.styles--selectable', context).click(function(e) {
      e.preventDefault();
      let widget = jQuery(this).data('widget');
      let style = jQuery(this).data('style');
      jQuery('.styles--selectable[data-widget=' + widget + ']').removeClass('styles--selected');
      jQuery(this).addClass('styles--selected');
      jQuery('#' + widget).val(style);
    });
  }
};
