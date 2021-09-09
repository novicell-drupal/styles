<?php
namespace Drupal\styles\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'styles_class' formatter.
 *
 * @FieldFormatter(
 *   id = "styles_class",
 *   label = @Translation("CSS class formatter"),
 *   field_types = {
 *     "styles"
 *   }
 * )
 */
class StylesClassFormatter extends FormatterBase {

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    /** @var \Drupal\styles\StylesManager $stylesManager */
    $stylesManager = \Drupal::service('styles.manager');
    $classes = $stylesManager->extractClasses($items);
    $elements[] = [
      '#markup' => implode(' ', $classes)
    ];

    return $elements;
  }

}
