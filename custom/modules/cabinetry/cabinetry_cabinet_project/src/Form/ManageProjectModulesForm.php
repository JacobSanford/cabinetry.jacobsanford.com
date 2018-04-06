<?php

namespace Drupal\cabinetry_cabinet_project\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\views\Views;

/**
 * ManageProjectModulesForm object.
 */
class ManageProjectModulesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cabinetry_cabinet_project_manage_project_modules_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $cabinetry_cabinet_project = NULL) {
    $form = [];

    /*
    // Existing modules manage display.
    $view = Views::getView('cabinetry_cabinet_project_manage_modules');
    $view->setDisplay('block_1');
    $view->setArguments([$node]);
    $render = $view->render();
    $form['manage_modules_view'] = $render;
     */

    // Add project modules.
    $form['add_project_module_button'] = [
      '#type' => 'link',
      '#title' => t('Add New Module'),
      '#url' => Url::fromRoute(
        'cabinetry_cabinet_project.add_project_module',
        [
          'cabinetry_cabinet_project' => is_numeric($cabinetry_cabinet_project) ? $cabinetry_cabinet_project : NULL,
        ]
      ),
      '#attributes' => [
        'class' => ['button'],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
