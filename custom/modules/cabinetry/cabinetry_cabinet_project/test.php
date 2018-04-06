<?php
error_reporting(-1);
ini_set("display_errors", 1);
ini_set('memory_limit','1024M');
$project = \Drupal::entityTypeManager()
  ->getStorage('cabinetry_cabinet_project')
  ->load(1);
/* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

$part = \Drupal::entityTypeManager()
  ->getStorage('cabinetry_part')
  ->load(121);
/* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */
$project->save();

print_r($project->getSheetParts());