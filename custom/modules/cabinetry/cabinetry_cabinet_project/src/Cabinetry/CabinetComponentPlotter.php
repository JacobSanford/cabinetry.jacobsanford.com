<?php

namespace Drupal\cabinetry_cabinet_project\Cabinetry;

use Drupal\cabinetry_cabinet_project\CabinetModuleInterface;
use Drupal\cabinetry_core\Cabinetry\CabinetryPlotter;
use Drupal\cabinetry_cabinet_project\Cabinetry\CabinetComponent;

/**
 * A generic object for plotting a cabinet component.
 */
class CabinetComponentPlotter extends CabinetryPlotter {

  /**
   * The sheet good to plot.
   *
   * @var \Drupal\cabinetry_cabinet_project\Cabinetry\CabinetComponent
   */
  public $module = NULL;

  /**
   * Constructor.
   *
   * @param \Drupal\cabinetry_cabinet_project\Cabinetry\CabinetComponent $module
   *   The CabinetComponent to plot.
   */
  public function __construct(CabinetComponent $module) {
    $this->module = $module;
    parent::__construct($module->module->getWidth() + 1, $module->module->getHeight() + 1, FALSE);
  }

  /**
   * Plot the sheet to the canvas.
   */
  public function plotModule() {
    $this->module->plotModule($this);
    return $this->writeImage('cabinetry_module');
  }

}
