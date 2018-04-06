<?php

namespace Drupal\cabinetry_cabinet_project\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;
use Drupal\Core\Entity\ContainerInterface;
use Drupal\cabinetry_cabinet_project\CabinetModuleListBuilderInterface;

/**
 * Provides a list controller for CabinetModule entity.
 *
 * @ingroup cabinetry_cabinet_project
 */
class CabinetModuleListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['table']['#empty'] = 'No modules have been added to this Cabinet Project yet.';

    // Add project modules.
    $build['add_modules_button'] = [
      '#type' => 'link',
      '#title' => t('Add New Module'),
      '#url' => Url::fromRoute(
        'cabinetry_cabinet_project.add_project_module',
        [
          'cabinetry_cabinet_project' => \Drupal::routeMatch()->getParameters()->get('cabinetry_cabinet_project'),
        ]
      ),
      '#attributes' => [
        'class' => ['button'],
      ],
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['name'] = $this->t('Name');
    $header['height'] = $this->t('Height');
    $header['width'] = $this->t('Width');
    $header['type'] = $this->t('Type');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\cabinetry_cabinet_project\CabinetModuleInterface */

    $project_eid = \Drupal::routeMatch()->getParameters()->get('cabinetry_cabinet_project');

    // Add module to entity reference.
    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($project_eid);
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    if ($project->hasCabinetModule($entity)) {
      /* @var $entity \Drupal\cabinetry_cabinet_project\Entity\CabinetModule */
      $row['id'] = $entity->id();
      $row['name'] = $entity->getName();
      $row['height'] = $entity->getHeight() . ' mm';
      $row['width'] = $entity->getWidth() . ' mm';
      $row['type'] = $entity->getClassLabel();
      return $row + parent::buildRow($entity);
    }
    return FALSE;
  }

}
