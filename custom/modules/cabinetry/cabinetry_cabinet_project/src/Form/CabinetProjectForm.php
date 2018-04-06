<?php

namespace Drupal\cabinetry_cabinet_project\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the cabinetry_cabinet_module entity edit forms.
 *
 * @ingroup cabinetry_cabinet_project
 */
class CabinetProjectForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {
    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    /* @var $entity \Drupal\cabinetry_cabinet_project\Entity\CabinetProject */
    $entity->save();

    // Clear and build project assets.
    $entity->batchRebuildParts();

    // Redirect back to cabinet module list.
    $form_state->setRedirect(
      'entity.cabinetry_cabinet_project.collection'
    );
  }

}
