<?php

namespace Drupal\styles\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\link\Plugin\Field\FieldWidget\LinkWidget;
use Drupal\styles\StylesManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'styles_link' widget.
 *
 * @FieldWidget(
 *   id = "styles_link",
 *   label = @Translation("Link with style"),
 *   description = @Translation("Link field with a style"),
 *   field_types = {
 *     "styles_link"
 *   }
 * )
 */
class StylesLinkWidget extends LinkWidget {

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

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = parent::defaultSettings();
    $settings['style'] = '';
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $value = $items[$delta]->getValue();
    $selected_style = $value['style'] ?? '';
    $collection_id = $this->getFieldSetting('collection');
    $collection = $this->stylesManager->getCollection($collection_id);
    $form_item_id = Html::getUniqueId('styles-widget');

    $element['#attached']['library'] = array_merge($element['#attached']['library'] ?? [], $collection->getLibraries(TRUE));

    $element['style'] = [
      '#type' => 'hidden',
      '#default_value' => $selected_style,
      '#attributes' => [
        'id' => $form_item_id
      ]
    ];

    $options = $this->stylesManager->getOptions($collection_id);
    $element['styles'] = [
      '#type' => 'item',
      '#title' => $this->t('Style', [], ['context' => 'Styles']),
      '#description' => $this->t('Select style to use to display the link')
    ];
    foreach ($options as $style => $label) {
      $element['styles'][$style] = [
        '#type' => 'container',
        0 => ['#markup' => $label]
      ];
      $element['styles'][$style]['#attributes']['data-widget'] = $form_item_id;
      $element['styles'][$style]['#attributes']['data-style'] = $style;
      $element['styles'][$style]['#attributes']['class'] = $collection->getPreviewClasses($style, ($selected_style == $style), FALSE, 'button');
    }

    return $element;
  }


}
