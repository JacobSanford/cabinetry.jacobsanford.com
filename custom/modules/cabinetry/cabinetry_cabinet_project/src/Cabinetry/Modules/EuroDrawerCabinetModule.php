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
use Drupal\taxonomy\Entity\Term;

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
   * The height of the drawer section.
   *
   * @var double
   */
  public $drawerSectionHeight = Null;

  /**
   * The height of the cabinet section.
   *
   * @var double
   */
  public $cabinetSectionHeight = Null;

  /**
   * Generate parts required to build this cabinet configuration.
   */
  protected function generateParts() {
    $this->parts = [];
    $this->setInnerWidth();
    $this->generateShelfParts();
    $this->generateSidePart(t('Left'));
    $this->generateSidePart(t('Right'));

    if ($this->counterOnTop == FALSE) {
      $this->generateTopBottomPart(t('Top'));
    }
    else {
      $this->generateTopSpreaders();
    }

    $this->generateTopBottomPart(t('Bottom'));
    $this->generateBackPanelPart();
    $this->generateDividerPanelParts();
    $this->generateNailerParts();

    // Drawers.
    $this->generateDrawerParts();
    if ($this->doorsAcrossGap > 0) {
      $this->generateDoorParts();
    }
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
      $terms = Term::loadMultiple($tids);
      $this->standardSlide = array_shift(array_values($terms));
    }
  }

  /**
   * Generate the doors for the cabinet.
   */
  protected function generateDoorParts() {
    $this->doors = [];
    $num_doors = $this->doorsAcrossGap;

    for ($door_counter = 0; $door_counter < $this->doorsAcrossGap; $door_counter++) {
      $door = new CabinetDoor(
        t(
          '[@module_name] Door @door_counter/@doors_total',
          [
            '@module_name' => $this->module->getName(),
            '@door_counter' => $door_counter + 1,
            '@doors_total' => $num_doors,
          ]
        ),
        ($this->width - ((1 + $this->doorsAcrossGap) * $this->doorReveal)) / $this->doorsAcrossGap,
        ($this->cabinetSectionHeight - (2 * $this->doorReveal)),
        $this->doorFrameStock,
        $this->doorFrameHeight,
        $this->doorFrameThickness,
        $this->doorFrameRouterBit,
        $this->doorPanelStock,
        $this->doorPanelUndersize
      );
      $this->doors[] = $door;

      // Hardware.
      if (!$this->project->getPurchaseDoors()) {
        $this->addParts($door->parts);
      }

      $this->hardware[] = $this->doorHinge;
      $this->hardware[] = $this->doorHinge;
      $this->hardware[] = $this->doorHingePlate;
      $this->hardware[] = $this->doorHingePlate;
    }
  }

  protected function generateDrawerParts() {
    $this->setStandardSlide();
    $distance_to_hole = (self::CABINET_DRAWER_32MM_UNITS * 32);
    $drawer_section_opening_height = $distance_to_hole
      - $this->carcassStock->getDepth()
      + $this->standardSlide->get('field_cabinetry_std_sld_hol_hab')->value;

    $this->drawerSectionHeight = $drawer_section_opening_height
      + ($this->carcassStock->getDepth() * 1.5);
    $this->cabinetSectionHeight = $this->height - $this->drawerSectionHeight;

    $drawer_height = $drawer_section_opening_height
      - $this->standardSlide->get('field_cabinetry_std_sld_vert_bc')->value
      - $this->standardSlide->get('field_cabinetry_std_sld_vert_tc')->value;
    $drawer_width = $this->width
      - (2 * $this->carcassStock->getDepth())
      - $this->standardSlide->get('field_cabinetry_std_sld_vert_tc')->value;
    $drawer_length = $this->standardSlide->get('field_cabinetry_std_sld_sug_dlen')->value;

    // Add drawer front.
    $door = new CabinetDoor(
      t(
        '[@module_name] Cabinet Drawer Front',
        [
          '@module_name' => $this->module->getName(),
        ]
      ),
      ($this->width - - (2 * $this->doorReveal)),
      ($this->drawerSectionHeight  - (2 * $this->doorReveal)),
      $this->doorFrameStock,
      $this->doorFrameHeight,
      $this->doorFrameThickness,
      $this->doorFrameRouterBit,
      $this->doorPanelStock,
      $this->doorPanelUndersize
    );
    $this->doors[] = $door;

    // Hardware.
    if (!$this->project->getPurchaseDoors()) {
      $this->addParts($door->parts);
    }

    // Add slides.
    $this->hardware[] = HardwareTerm::createFromTerm($this->standardSlide);
    $this->hardware[] = HardwareTerm::createFromTerm($this->standardSlide);
  }

}
