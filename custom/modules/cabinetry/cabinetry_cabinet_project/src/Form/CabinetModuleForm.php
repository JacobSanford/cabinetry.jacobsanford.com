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
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;
    /* @var $entity \Drupal\cabinetry_cabinet_project\Entity\CabinetModule */

    if ($cabinetry_cabinet_project == NULL) {
      // This has been likely called from Entity Operations field in table.
      $this->projectEid = $entity->getParentProject()->id();
    }
    else {
      $this->projectEid = $cabinetry_cabinet_project;
    }

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

    // Set customized elements not directly in form.
    $entity->setClassLabel($form['class']['#options'][$form_state->getValue('class')]);
    $entity->setClass($form_state->getValue('class'));

    $entity->save();

    // Add module to entity reference.
    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($this->projectEid);
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
        'cabinetry_cabinet_project' => $this->projectEid,
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $this->validateSheetSizes($form, $form_state);
    $this->validateSizeForDrawers($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  private function validateSheetSizes(array $form, FormStateInterface $form_state) {
    $module = $this->buildEntity($form, $form_state);
    /** @var \Drupal\cabinetry_cabinet_project\Entity\CabinetModule $module */

    // Add module to entity reference.
    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($this->projectEid);
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    // Validate size for carcass sheets.
    $carcass_sheet = $project->getCarcassMaterial();
    if ($carcass_sheet->getPreserveGrain()) {
      if ($module->getHeight() > $carcass_sheet->getWidth()) {
        $form_state->setErrorByName('height', t('Module height is larger than sheet goods allow.'));
      }
      $width_sheet_needed = $module->getWidth() - (2 * $carcass_sheet->getDepth());
      if ($width_sheet_needed > $carcass_sheet->getWidth()) {
        $form_state->setErrorByName('width', t('Module width is larger than sheet goods allow.'));
      }
      if ($module->getDepth() > $carcass_sheet->getHeight()) {
        $form_state->setErrorByName('depth', t('Module depth is larger than sheet goods allow.'));
      }
    }
    else {
      if ($module->getHeight() > $carcass_sheet->getWidth() && $module->getDepth() > $carcass_sheet->getWidth()) {
        $form_state->setErrorByName('height', t('Module height is larger than sheet goods allow.'));
      }
      $width_sheet_needed = $module->getWidth() - (2 * $carcass_sheet->getDepth());
      if ($width_sheet_needed > $carcass_sheet->getWidth() && $module->getDepth() > $carcass_sheet->getWidth()) {
        $form_state->setErrorByName('width', t('Module width is larger than sheet goods allow.'));
      }
      if ($module->getDepth() > $carcass_sheet->getHeight()) {
        $form_state->setErrorByName('depth', t('Module depth is larger than sheet goods allow.'));
      }
    }

    // Validate size for back sheets.
    $carcass_back = $project->getCarcassBackMaterial();
    $sheet_depth = $carcass_sheet->getDepth();
    $dado_depth = $sheet_depth / 2;
    $carcass_back_width = $module->getWidth() - (2 * $carcass_sheet->getDepth()) + (2 * $dado_depth);
    $carcass_back_height = $module->getHeight() - (2 * $carcass_sheet->getDepth()) + (2 * $dado_depth);

    if ($carcass_back->getPreserveGrain()) {
      if ($carcass_back_height > $carcass_sheet->getWidth()) {
        $form_state->setErrorByName('height', t('Module height is larger than sheet goods allow.'));
      }
      if ($carcass_back_width > $carcass_sheet->getHeight()) {
        $form_state->setErrorByName('width', t('Module width is larger than sheet goods allow.'));
      }
    }
    else {
      if ($carcass_back_height > $carcass_sheet->getWidth() && $carcass_back_height > $carcass_sheet->getHeight()) {
        $form_state->setErrorByName('height', t('Module height is larger than sheet goods allow.'));
      }
      if ($carcass_back_width > $carcass_sheet->getWidth() && $carcass_back_width > $carcass_sheet->getHeight()) {
        $form_state->setErrorByName('width', t('Module width is larger than sheet goods allow.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  private function validateSizeForDrawers(array $form, FormStateInterface $form_state) {
    $module = $this->buildEntity($form, $form_state);
    /** @var \Drupal\cabinetry_cabinet_project\Entity\CabinetModule $module */

    // Add module to entity reference.
    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($this->projectEid);
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    $classes_with_drawers = [
      'Drupal\cabinetry_cabinet_project\Cabinetry\Modules\EuroDrawerCabinetModule',
    ];

    if (in_array($module->getClass(), $classes_with_drawers)) {
      $maximum_drawer_length = $module->getDepth()
        - $project->getCarcassMaterial()->getDepth()
        - $project->getCarcassbackMaterial()->getDepth();

      $query = \Drupal::entityQuery('taxonomy_term')
        ->condition('vid', 'cabinetry_standard_slides')
        ->condition('field_cabinetry_std_min_cab_dept', $maximum_drawer_length, '<=')
        ->sort('field_cabinetry_std_min_cab_dept', 'DESC')
        ->range(0, 1);
      $tids = $query->execute();

      if (empty($tids)) {
        $form_state->setErrorByName('depth', t('Module depth is too small for standard drawer slides.'));
      }
    }
  }

}
