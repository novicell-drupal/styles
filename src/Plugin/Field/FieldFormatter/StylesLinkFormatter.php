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
 * Plugin implementation of the 'styles_link' formatter.
 *
 * @FieldFormatter(
 *   id = "styles_link",
 *   label = @Translation("Styled link"),
 *   field_types = {
 *     "styles_link"
 *   }
 * )
 */
class StylesLinkFormatter extends LinkFormatter {

  /**
   * @var StylesManager
   */
  protected $stylesManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('path.validator'),
      $container->get('styles.manager')
    );
  }

  /**
   * Constructs a new LinkFormatter.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Third party settings.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validator
   *   The path validator service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, PathValidatorInterface $path_validator, StylesManager $stylesManager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $path_validator);
    $this->stylesManager = $stylesManager;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $collection_id = $items->getSetting('collection');
    $collection = $this->stylesManager->getCollection($collection_id);

    /** @var StylesLinkType $item */
    foreach ($items as $delta => $item) {
      $values = $item->getValue();
      $values['options']['attributes']['class'] = array_merge($values['options']['attributes']['class'] ?? [], $this->stylesManager->extractClasses($items, 'style', $delta));
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
