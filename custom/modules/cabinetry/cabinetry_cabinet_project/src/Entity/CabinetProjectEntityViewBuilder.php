<?php

namespace Drupal\cabinetry_cabinet_project\Entity;

use Drupal\cabinetry_core\Entity\CabinetryProjectEntityViewBuilder;

class CabinetProjectEntityViewBuilder extends CabinetryProjectEntityViewBuilder {

  public static function setTemplateVariables(&$variables, $hook) {
    $variables = parent::setTemplateVariables($variables, $hook);

    if (!empty($variables['project']->getProjectPhoto()->entity)) {
      $variables['project_photo_uri'] = file_create_url($variables['project']->getProjectPhoto()->entity->getFileUri());
    }

    return $variables;
  }

}
