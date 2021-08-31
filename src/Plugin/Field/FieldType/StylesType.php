<?php
namespace Drupal\styles\Plugin\Field\FieldType;

use Drupal;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\options\Plugin\Field\FieldType\ListItemBase;
use Drupal\options\Plugin\Field\FieldType\ListStringItem;
use Drupal\styles\StylesManager;

/**
 * Plugin implementation of the Content Hierarchy field type.
 *
 * @FieldType(
 *   id = "styles",
 *   module = "styles",
 *   label = @Translation("Styles"),
 *   description = @Translation("Entity placement in the Content Hierarchy."),
 *   category = @Translation("Styles"),
 *   default_widget = "styles_select",
 *   default_formatter = "string"
 * )
 */
class StylesType extends ListStringItem {

  /**
   * @var StylesManager
   */
  protected $stylesManager;

  public function __construct(DataDefinitionInterface $definition, $name = NULL, TypedDataInterface $parent = NULL) {
    parent::__construct($definition, $name, $parent);
    $this->stylesManager = \Drupal::service('styles.manager');
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
        'collection' => '',
      ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $collection = $this->getSetting('collection');

    $element['collection'] = [
      '#type' => 'select',
      '#title' => t('Collection'),
      '#default_value' => $collection,
      '#options' => $this->stylesManager->getCollectionLabels(),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    $allowed_options = $this->stylesManager->getOptions($this->getSetting('collection'));
    return $allowed_options;
  }

  /**
   * {@inheritdoc}
   */
  public static function storageSettingsToConfigData(array $settings) {
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public static function storageSettingsFromConfigData(array $settings) {
    return $settings;
  }

  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    return parent::propertyDefinitions($field_definition);
  }
}
