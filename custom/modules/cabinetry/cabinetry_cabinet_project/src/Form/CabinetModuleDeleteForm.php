<?php

namespace Drupal\cabinetry_cabinet_project\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for deleting a cabinetry_cabinet_project entity.
 *
 * @ingroup cabinetry_cabinet_project
 */
class CabinetModuleDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t(
      'Are you sure you want to delete entity %name?',
      ['%name' => $this->entity->label()]
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelURL() {
    $parent_project =$this->getEntity()->getParentProject();
    /* @var $parent_project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    return Url::fromUri("internal:/cabinetry/cabinet_project/{$parent_project->id()}/modules");
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    /* @var $entity \Drupal\cabinetry_cabinet_project\Entity\CabinetModule */

    $parent_project = $entity->getParentProject();
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    $entity->delete();

    $parent_project->batchRebuildParts();

    \Drupal::logger('cabinetry_cabinet_project')->notice(
      '@type: deleted %title.',
      [
        '@type' => $this->entity->bundle(),
        '%title' => $this->entity->label(),
      ]
    );

    // Redirect back to cabinet module list.
    $form_state->setRedirect(
      'cabinetry_cabinet_project.manage_modules',
      [
        'cabinetry_cabinet_project' => $parent_project->id(),
      ]
    );
  }

}
