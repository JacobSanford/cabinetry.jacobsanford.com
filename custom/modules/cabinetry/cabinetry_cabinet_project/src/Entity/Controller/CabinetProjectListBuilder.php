<?php

namespace Drupal\cabinetry_cabinet_project\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Provides a list controller for CabinetProject entity.
 *
 * @ingroup cabinetry_cabinet_project
 */
class CabinetProjectListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['description'] = [
      '#markup' => $this->t('Description markup.'),
    ];

    $build += parent::render();
    $build['table']['#empty'] = 'No cabinet projects have been defined yet';

    // Add project modules.
    $build['add_projects_button'] = [
      '#type' => 'link',
      '#title' => t('Add New Project'),
      '#url' => Url::fromRoute(
        'entity.cabinetry_cabinet_project.add'
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
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\cabinetry_cabinet_project\Entity\CabinetProject */
    $row['id'] = $entity->id();
    $row['name'] = $entity->toLink($entity->getName());
    return $row + parent::buildRow($entity);
  }

}
