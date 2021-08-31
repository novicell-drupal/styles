<?php
namespace Drupal\styles\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'styles_class' formatter.
 *
 * @FieldFormatter(
 *   id = "styles_class",
 *   label = @Translation("Class formatter"),
 *   field_types = {
 *     "styles"
 *   }
 * )
 */
class StylesClassFormatter extends FormatterBase {

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#theme' => 'media_icon',
        '#icon' => $this->viewValue($item),
        '#width' => $this->getSetting('width'),
        '#height' => $this->getSetting('height')
      ];
    }

    return $elements;
  }

}
