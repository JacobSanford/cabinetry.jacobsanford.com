<?php

namespace Drupal\cabinetry_core\Entity;

use Drupal\cabinetry_core\Cabinetry\HardwareTerm;
use Drupal\cabinetry_core\Cabinetry\SheetStockTypeTerm;
use Drupal\cabinetry_core\Cabinetry\SolidStockTypeTerm;
use Drupal\Core\Entity\EntityViewBuilder;

class CabinetryProjectEntityViewBuilder extends EntityViewBuilder {

  public static function setTemplateVariables(&$variables, $hook) {
    setlocale(LC_MONETARY, 'en_US');

    // Entity is usually first key of variables.
    // @TODO Find a better way!
    $project_entity = array_shift($variables);
    $variables['project'] = $project_entity;

    \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->resetCache([$project_entity->id()]);

    $variables['summary_title'] = [
      '#prefix' => '<h2>',
      '#markup' => t('Bill of Goods'),
      '#suffix' => '</h2>',
    ];

    $variables['summary'] = [
      '#type' => 'table',
      '#header' => [
        t('Item'),
        t('Cost ea.'),
        t('Units'),
        t('Total'),
      ],
      '#empty' => t('No items in Bill of Goods for this Project.'),
      '#rows' => [],
    ];

    $total_bill_cost = 0;

    // Sheet items.
    foreach($project_entity->getCutSheets() as $cut_sheet) {
      /* @var $cut_sheet \Drupal\cabinetry_core\CutSheetInterface */

      $num_items = count($cut_sheet->getCutSheetImageIds());
      $material = $cut_sheet->getMaterial();

      if($material->bundle() == 'cabinetry_solid_stock') {
        $solid = SolidStockTypeTerm::createFromTerm($material);
        $each_cost = $cut_sheet->getWidth() * $cut_sheet->getHeight() * $cut_sheet->getDepth() * $solid->getMm3Cost();
      }
      else {
        $sheet = SheetStockTypeTerm::createFromTerm($material);
        $each_cost = $sheet->getCost();
      }

      $variables['summary']['#rows'][] = [
        $cut_sheet->getName(),
        '$' . money_format('%i', $each_cost),
        $num_items,
        '$' . money_format('%i', $each_cost * $num_items),
      ];
      $total_bill_cost += $each_cost * $num_items;
    }

    // Hardware items.
    $hardware_items = [];
    foreach($project_entity->getHardwareItems() as $hardware_item) {
      $item_wrapper = HardwareTerm::createFromTerm($hardware_item);
      if (empty($hardware_items[$item_wrapper->id()])) {
        $hardware_items[$item_wrapper->id()] = [
          'name' => $item_wrapper->getName(),
          'count' => 1,
          'price' => $item_wrapper->getPrice(),
        ];
      }
      else {
        $hardware_items[$item_wrapper->id()]['count'] = $hardware_items[$item_wrapper->id()]['count'] + 1;
      }
      $total_bill_cost += $item_wrapper->getPrice();
    }

    foreach ($hardware_items as $hardware_item) {
      $variables['summary']['#rows'][] = [
        $hardware_item['name'],
        '$' . money_format('%i', $hardware_item['price']),
        $hardware_item['count'],
        '$' . money_format('%i', $hardware_item['price'] * $hardware_item['count']),
      ];
    }

    $variables['summary']['#rows'][] = [
      NULL,
      NULL,
      t('Total'),
      '$' . money_format('%i', $total_bill_cost),
    ];

    $variables['sheet_title'] = [
      '#prefix' => '<h2>',
      '#markup' => t('Sheet Goods'),
      '#suffix' => '</h2>',
    ];

    $sheets_table = views_embed_view('cabinetry_cabinet_project_cut_sheets', 'block_1', $project_entity->id());
    $variables['sheet_list'] = [
      '#markup' => \Drupal::service('renderer')->render($sheets_table),
    ];

    $variables['solids_title'] = [
      '#prefix' => '<h2>',
      '#markup' => t('Solid Stock'),
      '#suffix' => '</h2>',
    ];

    $solids_table = views_embed_view('cabinetry_cabinet_project_cut_sheets', 'block_2', $project_entity->id());
    $variables['solids_list'] = [
      '#markup' => \Drupal::service('renderer')->render($solids_table),
    ];

    return $variables;
  }

}
