<?php

namespace Drupal\styles\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\Element\Checkboxes;
use Drupal\Core\Url;
use Drupal\link\Plugin\Field\FieldWidget\LinkWidget;
use Drupal\styles\StylesManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'styles_link_target' widget.
 *
 * @FieldWidget(
 *   id = "styles_link_target",
 *   label = @Translation("Styled link with target"),
 *   description = @Translation("Link field with a style"),
 *   field_types = {
 *     "styles_link_target"
 *   }
 * )
 */
class TargetableStylesLinkWidget extends StylesLinkWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = parent::defaultSettings();
    $settings['target_blank'] = 0;
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $value = $items[$delta]->getValue();

    $element['target_blank'] = [
      '#type' => 'checkbox',
      '#default_value' => !empty($value['target_blank']) ? $value['target_blank'] : 0,
      '#title' => $this->t('Open in a new window'),
    ];

    return $element;
  }

}
