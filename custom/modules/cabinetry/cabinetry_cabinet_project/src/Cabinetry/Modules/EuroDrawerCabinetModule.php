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
   * The distance from the top of cabinet to the slide holes.
   *
   * @var double
   */
  public $distanceTopToSlideHole = Null;

  /**
   * The drawer section opening height.
   *
   * @var double
   */
  public $drawerSectionOpeningHeight = Null;

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
    $this->setStandardSlide();

    // Drawers.
    $this->distanceTopToSlideHole = (self::CABINET_DRAWER_32MM_UNITS * 32);
    $this->drawerSectionOpeningHeight = $this->distanceTopToSlideHole
      - $this->carcassStock->getDepth()
      + $this->standardSlide->get('field_cabinetry_std_sld_hol_hab')->value;
    $this->drawerSectionHeight = $this->drawerSectionOpeningHeight
      + ($this->carcassStock->getDepth() * 1.5);
    $this->cabinetSectionHeight = $this->height - $this->drawerSectionHeight;

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
      $terms = \Drupal\taxonomy\Entity\Term::loadMultiple($tids);
      $unkeyed_terms = array_values($terms);
      $this->standardSlide = array_shift($unkeyed_terms);
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

      // Add drawer front.
      $door = new CabinetDoor(
        t(
          '[@module_name] Cabinet Drawer Front',
          [
            '@module_name' => $this->module->getName(),
          ]
        ),
        ($this->width - (2 * $this->doorReveal)),
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
    }
  }

  protected function generateDrawerParts() {
    $dado_depth = round($this->carcassStock->getDepth() * 0.40, 1);

    $drawer_height = $this->drawerSectionOpeningHeight
      - $this->standardSlide->get('field_cabinetry_std_sld_vert_bc')->value
      - $this->standardSlide->get('field_cabinetry_std_sld_vert_tc')->value;
    $drawer_width = $this->width
      - (2 * $this->carcassStock->getDepth())
      - $this->standardSlide->get('field_cabinetry_std_sld_vert_tc')->value;
    $drawer_length = $this->standardSlide->get('field_cabinetry_std_sld_sug_dlen')->value;

    $this->parts[] = CabinetryPart::createPart(
      t(
        '[@module_name] Drawer Back',
        [
          '@module_name' => $this->module->getName(),
        ]
      ),
      $this->carcassStock->getDepth(),
      $drawer_width -  (2 * $this->carcassStock->getDepth()),
      $drawer_height,
      $this->carcassStock,
      ''
    );
    $this->addBanding($this->carcassStock->getMaterial(), 2 * ($drawer_width -  (2 * $this->carcassStock->getDepth())));

    $this->parts[] = CabinetryPart::createPart(
      t(
        '[@module_name] Drawer Front',
        [
          '@module_name' => $this->module->getName(),
        ]
      ),
      $this->carcassStock->getDepth(),
      $drawer_width -  (2 * $this->carcassStock->getDepth()),
      $drawer_height,
      $this->carcassStock,
      ''
    );
    $this->addBanding($this->carcassStock->getMaterial(), 2 * ($drawer_width -  (2 * $this->carcassStock->getDepth())));

    $this->parts[] = CabinetryPart::createPart(
      t(
        '[@module_name] Drawer Left',
        [
          '@module_name' => $this->module->getName(),
        ]
      ),
      $this->carcassStock->getDepth(),
      $drawer_length,
      $drawer_height,
      $this->carcassStock,
      ''
    );
    $this->addBanding($this->carcassStock->getMaterial(), (2 * $drawer_length));
    $this->addBanding($this->carcassStock->getMaterial(), (2 * $drawer_height));

    $this->parts[] = CabinetryPart::createPart(
      t(
        '[@module_name] Drawer Right',
        [
          '@module_name' => $this->module->getName(),
        ]
      ),
      $this->carcassStock->getDepth(),
      $drawer_length,
      $drawer_height,
      $this->carcassStock,
      ''
    );
    $this->addBanding($this->carcassStock->getMaterial(), (2 * $drawer_length));
    $this->addBanding($this->carcassStock->getMaterial(), (2 * $drawer_height));

    $this->parts[] = CabinetryPart::createPart(
      t(
        '[@module_name] Drawer Bottom',
        [
          '@module_name' => $this->module->getName(),
        ]
      ),
      $this->carcassBackStock->getDepth(),
      $drawer_length
        - (2 * $this->carcassStock->getDepth())
        + (2 * $dado_depth),
      $drawer_width
        - (2 * $this->carcassStock->getDepth())
        + (2 * $dado_depth),
      $this->carcassBackStock,
      ''
    );

    // Drawer spreader
    $this->parts[] = CabinetryPart::createPart(
      t(
        '[@module_name] Drawer spreader',
        [
          '@module_name' => $this->module->getName(),
        ]
      ),
      $this->carcassStock->getDepth(),
      $this->carcassInnerWidth,
      CabinetProject::CABINET_PROJECT_CABINET_NAILER_HEIGHT,
      $this->carcassStock,
      'Two pocket holes on each end'
    );
    $this->addBanding($this->carcassStock->getMaterial(), $this->carcassInnerWidth);

    // Add slides.
    $this->hardware[] = HardwareTerm::createFromTerm($this->standardSlide);
    $this->hardware[] = HardwareTerm::createFromTerm($this->standardSlide);
  }

  /**
   * Plot a 2D image of this component.
   */
  public function plotModule($plotter) {
    $this->module->setDivisionRatios([
      $this->drawerSectionHeight / $this->height,
      $this->cabinetSectionHeight / $this->height,
    ]);
    parent::plotModule($plotter);
  }

}
