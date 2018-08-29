<?php

namespace Drupal\cabinetry_cabinet_project\Entity;

use Drupal\cabinetry_core\Entity\CabinetryProjectEntityViewBuilder;

/**
 * Provides Entity View data for Cabinet Project entities.
 *
 * @ingroup cabinetry
 */
class CabinetProjectEntityViewBuilder extends CabinetryProjectEntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public static function setTemplateVariables(&$variables, $hook) {
    $variables = parent::setTemplateVariables($variables, $hook);

    if (!empty($variables['project']->getProjectPhoto()->entity)) {
      $variables['project_photo_uri'] = file_create_url($variables['project']->getProjectPhoto()->entity->getFileUri());
    }

    $doors = $variables['project']->getDoors();
    $variables['door_table'] = '';

    if (!empty($doors)) {
      $door_table_header = ['Name', 'Width', 'Height'];
      $door_table_data = [];
      foreach ($doors as $module_label => $door_list) {
        foreach ($door_list as $door_id => $cur_door) {
          $door_table_data[] = [
            "$cur_door->label",
            $cur_door->width . 'mm',
            $cur_door->height . 'mm',
          ];
        }
      }
      $table = [
        '#theme' => 'table',
        '#cache' => ['disabled' => TRUE],
        '#header' => $door_table_header,
        '#rows' => $door_table_data,
        '#prefix' => '<h2>' . t('Doors') . '</h2>',
      ];
      $variables['door_table_html'] = render($table);
    }

    return $variables;
  }

}
