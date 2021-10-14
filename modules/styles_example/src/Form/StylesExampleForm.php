<?php
namespace Drupal\styles_example\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class StylesExampleForm extends FormBase {

  public function getFormId() {
    return 'styles_example';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];

    $form['styles'] = [
      '#type' => 'styles',
      '#title' => 'Styles',
      '#description' => 'Styles form element.',
      '#collection' => 'color_theme_example',
      '#multiple' => TRUE,
      '#default_value' => 'primary'
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit')
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
