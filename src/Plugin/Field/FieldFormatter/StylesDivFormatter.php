<?php
namespace Drupal\styles\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\styles\StylesManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'styles_div' formatter.
 *
 * @FieldFormatter(
 *   id = "styles_div",
 *   label = @Translation("Div formatter"),
 *   field_types = {
 *     "styles"
 *   }
 * )
 */
class StylesDivFormatter extends FormatterBase {

  protected $stylesManager;

  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, StylesManager $stylesManager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->stylesManager = $stylesManager;
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($plugin_id, $plugin_definition, $configuration['field_definition'], $configuration['settings'], $configuration['label'], $configuration['view_mode'], $configuration['third_party_settings'], $container->get('styles.manager'));
  }

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    $classes = $this->stylesManager->extractClasses($items);
    $elements[] = [
      '#type' => 'container',
      '#attached' => ['library' => $this->stylesManager->getCollection($items->getFieldDefinition()->getSetting('collection'))->getLibraries()],
      '#attributes' => ['class' => $classes]
    ];

    return $elements;
  }

}
