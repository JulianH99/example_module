<?php

namespace Drupal\example_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ExampleModuleForm.
 */
class ExampleModuleForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'example_module_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['nombre'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre'),
      '#description' => $this->t('Nombre del usuario'),
      '#maxlength' => 150,
      '#size' => 64,
      '#required' => TRUE,
      '#weight' => '0',
    ];
    $form['identificacion'] = [
      '#type' => 'number',
      '#title' => $this->t('Identificacion'),
      '#description' => $this->t('Identificacion del usuario'),
      '#required' => TRUE,
      '#weight' => '0',
    ];
    $form['fecha_nacimiento'] = [
      '#type' => 'date',
      '#title' => $this->t('Fecha de nacimiento'),
      '#description' => $this->t('Fecha de nacimiento en formato \'dia-mes-año\'. Ej: 24-11-1999'),
      '#weight' => '0',
      '#format' => 'd-M-Y'
    ];
    $form['cargo'] = [
      '#type' => 'select',
      '#title' => $this->t('Cargo'),
      '#description' => $this->t('Cargo del usuario'),
      '#size' => 5,
      '#weight' => '0',
      '#options' => [
        'administrador' => 'Administrador',
        'webmaster' => 'Webmaster',
        'desarrollador' => 'Desarrollador'
      ]
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {

    $values = $form_state->getValues();

    if (!$values['nombre']) {
      $form_state->setErrorByName('nombre', 'El nombre es un campo requerido');
    }

    if (!$values['identificacion'] || !is_numeric($values['identificacion'])) {
      $form_state->setErrorByName('identificacion', 'Debe ingresar una identificacion válida');
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // Display result.
    $values = $form_state->getValues();

    // throw new \Exception("this is me bitch");

    $connection = \Drupal::service('database');

    $result = $connection->insert('example_users')
      ->fields([
        'nombre' => $values['nombre'],
        'identificacion' => $values['identificacion'],
        'fecha_nacimiento' => $values['fecha_nacimiento'],
        'cargo' => $values['cargo'],
        'estado' => $values['cargo'] === 'administrador' ? 1 : 0
      ])
      ->execute();

    if ($result) {
      \Drupal::messenger()->addMessage('Sus datos se han enviado correctamente');
    } else {
      \Drupal::messenger()->addWarning('Ha ocurrido un error al procesar los datos enviados. Por favor intente de nuevo más tarde');
    }
  }
}
