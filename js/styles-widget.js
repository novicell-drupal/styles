Drupal.behaviors.styles_widget = {
  attach: function (context, settings) {
    jQuery('.styles--selectable', context).once('styles--selectable').click(function(e) {
      e.preventDefault();
      let widget = jQuery('#' + jQuery(this).data('widget'));
      let style = jQuery(this).data('style');
      jQuery('.styles--selectable[data-widget=' + jQuery(this).data('widget') + ']').removeClass('styles--selected');
      jQuery(this).addClass('styles--selected');
      widget.find('input[type=checkbox]').prop( 'checked', false );
      if (!jQuery(this).hasClass('styles--empty')) {
        widget.find('input[value=' + style + ']').prop('checked', true);
      }
    });
    jQuery('.styles--toggleable', context).once('styles--toggleable').click(function(e) {
      e.preventDefault();
      let widget = jQuery('#' + jQuery(this).data('widget'));
      let style = jQuery(this).data('style');
      if (jQuery(this).hasClass('styles--selected')) {
        jQuery(this).removeClass('styles--selected');
        widget.find('input[value=' + style + ']').prop( 'checked', false );
      } else {
        jQuery(this).addClass('styles--selected');
        widget.find('input[value=' + style + ']').prop( 'checked', true );
      }
    });
  }
};
