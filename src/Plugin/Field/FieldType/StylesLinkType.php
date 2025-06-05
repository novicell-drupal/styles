<?php

namespace Drupal\styles\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\link\Plugin\Field\FieldType\LinkItem;
use Drupal\styles\StylesManager;

/**
 * Plugin implementation of the 'styles_link' field type.
 *
 * @FieldType(
 *   id = "styles_link",
 *   label = @Translation("Styled link"),
 *   description = @Translation("Link field with a style"),
 *   category = "styles",
 *   default_widget = "styles_link",
 *   default_formatter = "styles_link"
 * )
 */
class StylesLinkType extends LinkItem {

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

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);
    $properties['style'] = DataDefinition::create('string')
      ->setLabel(t('Styles'))
      ->addConstraint('Length', ['max' => 255])
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);
    $schema['columns']['style'] = [
      'type' => 'varchar',
      'length' => '255',
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::fieldSettingsForm($form, $form_state);

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values = parent::generateSampleValue($field_definition);

    /** @var StylesManager $stylesManager */
    $stylesManager = \Drupal::service('styles.manager');
    $options = array_values($stylesManager->getOptions($field_definition->getSetting('collection')));
    $values['style'] = $options[rand(0, count($options) - 1)];

    return $values;
  }
}
