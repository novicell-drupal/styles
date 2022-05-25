<?php

namespace Drupal\styles\Plugin\Transform\Field;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\styles\StylesManager;
use Drupal\transform_api\Annotation\FieldTransform;
use Drupal\transform_api\FieldTransformBase;
use Drupal\transform_api\FieldTransformInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @FieldTransform(
 *  id = "styles",
 *  title = "Styles",
 *  description = "Styles as CSS classes",
 *  types = {
 *    "styles"
 *  }
 * )
 */
class Styles extends FieldTransformBase {

  protected $stylesManager;

  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $view_mode, array $third_party_settings, StylesManager $stylesManager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $view_mode, $third_party_settings);
    $this->stylesManager = $stylesManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($plugin_id, $plugin_definition, $configuration['field_definition'], $configuration['settings'], $configuration['view_mode'], $configuration['third_party_settings'], $container->get('styles.manager'));
  }

  /**
   * @inheritDoc
   */
  public function transformElements(FieldItemListInterface $items, $langcode): array {
    return $this->stylesManager->extractClasses($items);
  }
}
