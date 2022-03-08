<?php

namespace Drupal\styles\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;
use Drupal\link\Plugin\Field\FieldFormatter\LinkSeparateFormatter;
use Drupal\styles\Plugin\Field\FieldType\StylesLinkType;
use Drupal\styles\StylesManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'styles_link_target' formatter.
 *
 * @FieldFormatter(
 *   id = "styles_link_target",
 *   label = @Translation("Styled link with target"),
 *   field_types = {
 *     "styles_link_target"
 *   }
 * )
 */
class TargetableStylesLinkFormatter extends StylesLinkFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $collection_id = $items->getSetting('collection');
    $collection = $this->stylesManager->getCollection($collection_id);

    /** @var StylesLinkType $item */
    foreach ($items as $delta => $item) {
      $values = $item->getValue();
      $values['options']['attributes']['class'] = ($values['options']['attributes']['class'] ?? []) + $this->stylesManager->extractClasses($items, 'style', $delta);
      $values['options']['attributes']['target'] = ($items->get($delta)->get('target_blank')->getValue() == 1 ? '_blank' : '');
      $item->setValue($values);
    }
    $element = parent::viewElements($items, $langcode);
    /** @var StylesLinkType $item */
    foreach ($items as $delta => $item) {
      $element[$delta]['#attached']['library'] = array_merge($element[$delta]['#attached']['library'] ?? [], $collection->getLibraries());
    }

    return $element;
  }
}
