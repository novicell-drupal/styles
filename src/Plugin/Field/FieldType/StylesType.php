<?php
namespace Drupal\styles\Plugin\Field\FieldType;

use Drupal;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\OptGroup;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\OptionsProviderInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\options\Plugin\Field\FieldType\ListItemBase;
use Drupal\options\Plugin\Field\FieldType\ListStringItem;
use Drupal\styles\StylesManager;
use Drupal\user\UserInterface;

/**
 * Plugin implementation of the Content Hierarchy field type.
 *
 * @FieldType(
 *   id = "styles",
 *   module = "styles",
 *   label = @Translation("Styles"),
 *   description = @Translation("Field with one or more styles"),
 *   category = @Translation("Styles"),
 *   default_widget = "styles_select",
 *   default_formatter = "string"
 * )
 */
class StylesType extends FieldItemBase implements OptionsProviderInterface {

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
  public static function storageSettingsToConfigData(array $settings) {
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public static function storageSettingsFromConfigData(array $settings) {
    return $settings;
  }

  /**$collection
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values = parent::generateSampleValue($field_definition);
    /** @var StylesManager $stylesManager */
    $stylesManager = \Drupal::service('styles.manager');
    $options = array_values($stylesManager->getOptions($field_definition->getSetting('collection')));
    $values['value'] = $options[rand(0, count($options) - 1)];
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return empty($this->value) && (string) $this->value !== '0';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Styles'))
      ->addConstraint('Length', ['max' => 255])
      ->setRequired(TRUE);

    return $properties;
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'varchar',
          'length' => 255,
        ],
      ],
      'indexes' => [
        'value' => ['value'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(AccountInterface $account = NULL) {
    // Flatten options firstly, because Possible Options may contain group
    // arrays.
    $flatten_options = OptGroup::flattenOptions($this->getPossibleOptions($account));
    return array_keys($flatten_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return $this->getSettableOptions($account);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    // Flatten options firstly, because Settable Options may contain group
    // arrays.
    $flatten_options = OptGroup::flattenOptions($this->getSettableOptions($account));
    return array_keys($flatten_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    return $this->stylesManager->getOptions($this->getSetting('collection'));
  }

}
