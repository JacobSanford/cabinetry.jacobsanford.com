<?php

namespace Drupal\cabinetry_cabinet_project\Entity\Controller;

class CabinetProjectTitleBuilder {

  /**
   * {@inheritdoc}
   */
  public static function getTitle() {
    $project = \Drupal::routeMatch()->getParameters()->get('cabinetry_cabinet_project');

    if (is_object($project)) {
      /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */
      return $project->getName();
    }

    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($project);
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    return $project->getName();
  }

}
