<?php

namespace Drupal\cabinetry_cabinet_project\Entity\Controller;

class CabinetModuleTitleBuilder {

  /**
   * {@inheritdoc}
   */
  public static function getTitle() {
    $project_id = \Drupal::routeMatch()->getParameters()->get('cabinetry_cabinet_project');
    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($project_id);
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    return t(
      '@project_name - Cabinet Modules',
      [
        '@project_name' => $project->getName(),
      ]
    );
  }

}
