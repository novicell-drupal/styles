<?php

namespace Drupal\styles\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\link\Plugin\Field\FieldType\LinkItem;
use Drupal\styles\StylesManager;

/**
 * Plugin implementation of the 'styles_link_target' field type.
 *
 * @FieldType(
 *   id = "styles_link_target",
 *   label = @Translation("Styled link with target"),
 *   description = @Translation("Link field with a style and target"),
 *   category = @Translation("Styles"),
 *   default_widget = "styles_link_target",
 *   default_formatter = "styles_link_target"
 * )
 */
class TargetableStylesLinkType extends StylesLinkType {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);
    $properties['target_blank'] = DataDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Open in a new window'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);
    $schema['columns']['target_blank'] = [
      'type' => 'int',
      'size' => 'tiny',
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

    $values['target_blank'] = (rand(0, 1) == 1);

    return $values;
  }
}
