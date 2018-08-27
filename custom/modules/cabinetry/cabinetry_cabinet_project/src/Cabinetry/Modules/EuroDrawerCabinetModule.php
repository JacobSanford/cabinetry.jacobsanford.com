<?php

namespace Drupal\cabinetry_cabinet_project\Cabinetry\Modules;

use Drupal\cabinetry_cabinet_project\CabinetModuleInterface;
use Drupal\cabinetry_cabinet_project\Cabinetry\Modules\BasicEuroCabinetModule;
use Drupal\cabinetry_cabinet_project\CabinetProjectInterface;


use Drupal\cabinetry_cabinet_project\Cabinetry\CabinetComponent;
use Drupal\cabinetry_cabinet_project\Cabinetry\CabinetDoor;
use Drupal\cabinetry_cabinet_project\Entity\CabinetProject;
use Drupal\cabinetry_core\Cabinetry\RouterBitTerm;
use Drupal\cabinetry_core\Entity\CabinetryPart;
use Drupal\cabinetry_core\Entity\StockItem;
use Drupal\cabinetry_core\Cabinetry\HardwareTerm;

/**
 * A generic object to serve as a basic european styled cabinet module.
 */
class EuroDrawerCabinetModule extends BasicEuroCabinetModule {

  const CABINET_DRAWER_32MM_UNITS = 6.0;

  /**
   * The number of shelves.
   *
   * @var /Drupal/
   */
  public $standardSlide = Null;

  /**
   * {@inheritdoc}
   */
  public function build() {
    $this->height = (float) $this->module->getHeight();
    $this->width = (float) $this->module->getWidth();
    $this->depth = (float) $this->module->getDepth();

    // Carcass properties.
    $this->numShelves = (int) $this->module->getNumShelves();
    $this->counterOnTop = (bool) $this->module->getCounterOnTop();

    // Door properties.
    $this->doorFrameThickness = (float) $this->project->getDoorFrameStockThickness();
    $this->doorFrameHeight = (float) $this->project->getDoorFrameHeight();
    $this->doorFrameRouterBit = RouterBitTerm::createFromTerm($this->project->getDoorRouterBit());
    $this->doorReveal = (float) $this->project->getDoorReveal();
    $this->doorPanelUndersize = (float) $this->project->getDoorPanelUndersize();
    $this->doorsAcrossGap = (int) $this->module->getDoorsAcrossGap();
    $this->doorHinge = HardwareTerm::createFromTerm($this->project->getPrimaryHinge());
    $this->doorHingePlate = HardwareTerm::createFromTerm($this->project->getPrimaryHingePlate());

    // Determine hinge to use
    $this->setStandardSlide();

    // Drawers.

    // Lay out sections.

    // Generate parts for cabinet.
    $this->generateParts();
  }

  /**
   * {@inheritdoc}
   */
  private function setStandardSlide() {
    $maximum_drawer_length = $this->depth
      - $this->carcassStock->getDepth()
      - $this->carcassBackStock->getDepth();

    $query = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', 'cabinetry_standard_slides')
      ->condition('field_cabinetry_std_min_cab_dept', $maximum_drawer_length, '<=')
      ->sort('field_cabinetry_std_min_cab_dept', 'DESC')
      ->range(0, 1);

    $tids = $query->execute();

    if (!empty($tids)) {
      $terms = \Drupal\taxonomy\Entity\Term::loadMultiple($tids);
      $this->standardSlide = array_shift(array_values($terms));
    }
  }

}
