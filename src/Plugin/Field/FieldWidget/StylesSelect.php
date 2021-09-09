<?php
namespace Drupal\styles\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsSelectWidget;

/**
 * Plugin implementation of the 'styles select' widget.
 *
 * @FieldWidget(
 *   id = "styles_select",
 *   label = @Translation("Select list"),
 *   description = @Translation("Styles value select list"),
 *   field_types = {
 *     "styles"
 *   },
 *   multiple_values = TRUE
 * )
 */
class StylesSelect extends OptionsSelectWidget {}
