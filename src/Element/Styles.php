<?php
namespace Drupal\styles\Element;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a Styles form element.
 *
 * The #default_value accepted by this element is an ID of a style or array of style IDs.
 *
 * @FormElement("styles")
 *
 * Usage can include the following components:
 *
 *   $element['background_style'] = [
 *     '#type' => 'styles',
 *     '#collection' => 'backgrounds',
 *     '#title' => t('Select background'),
 *     '#default_value' => 'blue',
 *     '#description' => t('Select a background style to use.'),
 *     '#multiple' => FALSE,
 *   ];
 */
class Styles extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);

    return [
      '#input' => TRUE,
      '#tree' => TRUE,
      '#multiple' => FALSE,
      '#collection' => NULL,
      '#preview_type' => NULL,
      '#process' => [
        [$class, 'processAjaxForm'],
        [$class, 'processStyles'],
        [$class, 'processGroup'],
      ],
      '#pre_render' => [
        [$class, 'preRenderGroup'],
      ],
      '#element_validate' => [
        [$class, 'elementValidateStyles'],
      ],
      '#theme' => 'styles_element',
      '#theme_wrappers' => ['form_element'],
    ];
  }

  /**
   * Expand the styles_element into it's required sub-elements.
   *
   * @param array $element
   *   The base form element render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   * @param array $complete_form
   *   The complete form render array.
   *
   * @return array
   *   The form element render array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function processStyles(array &$element, FormStateInterface $form_state, array &$complete_form): array {
    $default_value = [];

    if (!empty($element['#value'])) {
      $default_value = $element['#value'];
      if (is_string($default_value)) {
        $default_value = [$default_value];
      }
    }

    $stylesManager = \Drupal::service('styles.manager');
    $collection_id = $element['#collection'];
    $collection = $stylesManager->getCollection($collection_id);
    $form_item_id = $element['#id'];
    $multiple = $element['#multiple'];

    $element = array_merge(
      $element,
      [
        '#attached' => [
          'library' => $collection->getLibraries(TRUE)
        ],
        '#wrapper_attributes' => [
          'class' => [
            'form-styles',
          ],
        ],
      ]
    );

    $options = $stylesManager->getOptions($collection_id);
    $element['style'] = [
      '#type' => 'checkboxes',
      '#id' => $form_item_id,
      '#name' => $element['#name'],
      '#default_value' => $default_value ?? [],
      '#options' => $options,
    ];

    $element['styles'] = [
      '#type' => 'container',
    ];
    foreach ($options as $style => $label) {
      $element['styles'][$style] = [
        '#theme' => 'styles_preview',
        '#collection' => $collection_id,
        '#style' => $style,
        '#form_item_id' => $form_item_id,
        '#active' => in_array($style, $default_value),
        '#toggle' => $multiple
      ];
    }
    if (!$element['#required'] && !$multiple) {
      $element['styles']['_none'] = [
        '#theme' => 'styles_preview',
        '#collection' => $collection_id,
        '#form_item_id' => $form_item_id,
        '#active' => empty($default_value),
      ];
    }

    return $element;
  }

  /**
   * Extract the proper portion of our default_value.
   *
   * @param array $element
   *   The render element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   * @param array $complete_form
   *   The complete form render array.
   */
  public static function elementValidateStyles(array &$element, FormStateInterface $form_state, array &$complete_form) {
    $value = NestedArray::getValue($form_state->getValues(), $element['style']['#parents']);
    if (!empty($value)) {
      $value = array_filter($value);
      if (count($value) === 1) {
        $value = reset($value);
      }
    } else {
      $value = NULL;
    }

    $form_state->setValueForElement($element, $value);
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    $value = NULL;
    // Process the submission of our form element.
    if ($input !== FALSE && $input !== NULL && isset($input['style'])) {
      $value = $input['style'];
    }
    elseif ($input === FALSE) {
      if (!empty($element['#default_value'])) {
        $value = $element['#default_value'];
      }
    }

    if (!empty($value)) {
      if (!$element['#multiple'] && is_array($value)) {
        $value = $value[array_key_first($value)];
      }
      if (is_array($value)) {
        $value = array_values($value);
      }

      // Normalize 0 value.
      $value = ($value === 0) ? '' : $value;
    }

    return $value;
  }
}
