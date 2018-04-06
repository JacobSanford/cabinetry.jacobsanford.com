<?php

namespace Drupal\cabinetry_cabinet_project\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the cabinetry_cabinet_module entity edit forms.
 *
 * @ingroup cabinetry_cabinet_project
 */
class CabinetModuleForm extends ContentEntityForm {

  protected $projectEid;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $cabinetry_cabinet_project = NULL) {
    /* @var $entity \Drupal\cabinetry_cabinet_project\Entity\CabinetModule */
    $this->projectEid = $cabinetry_cabinet_project;

    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    $form['class'] = [
      '#type' => 'select',
      '#title' => $this->t('Cabinet Type'),
      '#weight' => -10,
      '#options' => _cabinetry_cabinet_project_project_cabinet_types($this->projectEid),
      '#default_value' => $entity->getClass(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    // Set custom entity properties.
    $entity = $this->getEntity();
    /* @var $entity \Drupal\cabinetry_cabinet_project\Entity\CabinetModule */

    if ($this->projectEid == NULL) {
      // This has been called from Entity Operations field in table.
      $project_eid = $entity->getParentProject()->id();
    }
    else {
      $project_eid = $this->projectEid;
    }

    // Set customized elements not directly in form.
    $entity->setClassLabel($form['class']['#options'][$form_state->getValue('class')]);
    $entity->setClass($form_state->getValue('class'));

    $entity->save();

    // Add module to entity reference.
    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($project_eid);
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    $new_item = TRUE;

    foreach ($project->getCabinetModuleIds() as $module_id) {
      if ($module_id == $entity->id()) {
        $new_item = FALSE;
        break;
      }
    }
    if ($new_item) {
      $project->addCabinetModule($entity);
    }

    $project->save();
    $project->batchRebuildParts();

    // Redirect back to cabinet module list.
    $form_state->setRedirect(
      'cabinetry_cabinet_project.manage_modules',
      [
        'cabinetry_cabinet_project' => $project_eid,
      ]
    );
  }

}
