<?php

namespace Drupal\styles\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Checkboxes;
use Drupal\styles\StylesManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'styles' widget.
 *
 * @FieldWidget(
 *   id = "styles",
 *   label = @Translation("Style preview"),
 *   description = @Translation("Select from previews of styles"),
 *   field_types = {
 *     "styles"
 *   },
 *   multiple_values = TRUE
 * )
 */
class StylesWidget extends WidgetBase {

  /**
   * @var StylesManager
   */
  protected $stylesManager;

  /**
   * Constructs a WidgetBase object.
   *
   * @param string $plugin_id
   *   The plugin_id for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param array $settings
   *   The widget settings.
   * @param array $third_party_settings
   *   Any third party settings.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, StylesManager $stylesManager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->stylesManager = $stylesManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($plugin_id, $plugin_definition, $configuration['field_definition'], $configuration['settings'], $configuration['third_party_settings'], $container->get('styles.manager'));
  }

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = $items->getValue() ?? [];
    $values = [];
    foreach ($value as $item) {
      if (isset($item['value'])) {
        $values[] = $item['value'];
      }
    }
    $collection_id = $this->getFieldSetting('collection');
    $collection = $this->stylesManager->getCollection($collection_id);
    $form_item_id = Html::getUniqueId('styles-widget');
    $multiple = $this->fieldDefinition->getFieldStorageDefinition()->isMultiple();

    $element['#attached']['library'] = array_merge($element['#attached']['library'] ?? [], $collection->getLibraries(TRUE));

    $options = $this->stylesManager->getOptions($collection_id);
    $element += [
      '#type' => 'item',
    ];

    $element['style'] = [
      '#type' => 'checkboxes',
      '#id' => $form_item_id,
      '#default_value' => $values ?? [],
      '#options' => $options,
    ];

    foreach ($options as $style => $label) {
      $element['styles'][$style] = [
        '#theme' => 'styles_preview',
        '#collection' => $collection_id,
        '#style' => $style,
        '#form_item_id' => $form_item_id,
        '#active' => in_array($style, $values),
        '#toggle' => $multiple
      ];
    }
    if (!$element['#required'] && !$multiple) {
      $element['styles']['_none'] = [
        '#theme' => 'styles_preview',
        '#collection' => $collection_id,
        '#form_item_id' => $form_item_id,
        '#active' => empty($values),
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    return Checkboxes::getCheckedCheckboxes($values['style']);
  }
}
