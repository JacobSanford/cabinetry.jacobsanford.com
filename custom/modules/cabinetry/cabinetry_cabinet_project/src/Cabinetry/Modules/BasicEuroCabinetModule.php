<?php

namespace Drupal\cabinetry_cabinet_project\Cabinetry\Modules;

use Drupal\cabinetry_cabinet_project\CabinetModuleInterface;
use Drupal\cabinetry_cabinet_project\CabinetProjectInterface;
use Drupal\cabinetry_cabinet_project\Cabinetry\CabinetComponent;
use Drupal\cabinetry_cabinet_project\Cabinetry\CabinetDoor;
use Drupal\cabinetry_cabinet_project\Entity\CabinetProject;
use Drupal\cabinetry_core\Cabinetry\HardwareTerm;
use Drupal\cabinetry_core\Cabinetry\RouterBitTerm;
use Drupal\cabinetry_core\Entity\CabinetryPart;
use Drupal\cabinetry_core\Entity\StockItem;

/**
 * A generic object to serve as a basic european styled cabinet module.
 */
class BasicEuroCabinetModule extends CabinetComponent {

  /**
   * The back panel sheet good.
   *
   * @var \Drupal\cabinetry_core\StockItemInterface
   */
  public $carcassBackStock = NULL;

  /**
   * The inner width dim, in millimeters.
   *
   * @var float
   */
  public $carcassInnerWidth = 0.0;

  /**
   * The carcass sheet good.
   *
   * @var \Drupal\cabinetry_core\StockItemInterface
   */
  public $carcassStock = NULL;

  /**
   * An array of float values equal to or less than 1.0.
   *
   * The sum of the array values should total 1.0.
   *
   * @var float[]
   */
  public $divisions = [];

  /**
   * The doors attached to this module.
   *
   * @var \Drupal\cabinetry_cabinet_project\Cabinetry\CabinetDoor[]
   */
  public $doors = [];

  /**
   * The number of doors spanning the carcass opening.
   *
   * @var int
   */
  public $doorsAcrossGap = 0;

  /**
   * The door reveal, in millimeters.
   *
   * @var float
   */
  public $doorReveal = 0.0;

  /**
   * The door panel undersize for expansion, each side, in millimeters.
   *
   * @var float
   */
  public $doorPanelUndersize = 0.0;

  /**
   * The door frame material.
   *
   * @var \Drupal\cabinetry_core\StockItemInterface
   */
  public $doorFrameStock = NULL;

  /**
   * The door frame material thickness.
   *
   * @var float
   */
  public $doorFrameThickness = NULL;

  /**
   * The door frame material height.
   *
   * @var float
   */
  public $doorFrameHeight = NULL;

  /**
   * The door frame rail/stile bit.
   *
   * @var \Drupal\cabinetry_core\Cabinetry\RouterBitTerm
   */
  public $doorFrameRouterBit = NULL;

  /**
   * The door panel material.
   *
   * @var \Drupal\cabinetry_core\StockItemInterface
   */
  public $doorPanelStock = NULL;

  /**
   * The door hinge.
   *
   * @var \Drupal\cabinetry_core\Cabinetry\HardwareTerm
   */
  public $doorHinge = NULL;

  /**
   * The door hinge plate.
   *
   * @var \Drupal\cabinetry_core\Cabinetry\HardwareTerm
   */
  public $doorHingePlate = NULL;

  /**
   * The drawerSlides.
   *
   * @var \Drupal\cabinetry_core\Cabinetry\HardwareTerm
   */
  public $drawerSlides = NULL;

  /**
   * The drawer Rail height.
   *
   * @var float
   */
  public $drawerRailHeight = NULL;

  /**
   * The drawer Rail height.
   *
   * @var float
   */
  public $drawerBottomMaterial = NULL;

  /**
   * The number of shelves.
   *
   * @var int
   */
  public $numShelves = 0;

  /**
   * The number of shelves.
   *
   * @var bool
   */
  public $counterOnTop = FALSE;

  /**
   * The parent project for this module.
   *
   * @var \Drupal\cabinetry_cabinet_project\CabinetProjectInterface
   */
  public $project = NULL;

  /**
   * The CabinetModule or child entity to build.
   *
   * @var \Drupal\cabinetry_cabinet_project\CabinetModuleInterface
   */
  public $module = NULL;

  /**
   * Constructor.
   *
   * @param \Drupal\cabinetry_cabinet_project\CabinetProjectInterface $project
   *   The parent project for this module.
   * @param \Drupal\cabinetry_cabinet_project\CabinetModuleInterface $module
   *   The CabinetModule or child entity to build.
   */
  public function __construct(CabinetProjectInterface $project, CabinetModuleInterface $module) {
    $this->project = $project;
    $this->module = $module;
    $this->setupStock();
  }

  /**
   * Setup stock properties for building cabinet.
   */
  private function setupStock() {
    $carcass_material = $this->project->getCarcassMaterial();
    $this->carcassStock = StockItem::createLoadItem(
      $carcass_material->getName(),
      $carcass_material->getDepth(),
      $carcass_material->getWidth(),
      $carcass_material->getHeight(),
      $carcass_material->getEntity(),
      $carcass_material->getPreserveGrain()
    );

    $carcass_back_material = $this->project->getCarcassBackMaterial();
    $this->carcassBackStock = StockItem::createLoadItem(
      $carcass_back_material->getName(),
      $carcass_back_material->getDepth(),
      $carcass_back_material->getWidth(),
      $carcass_back_material->getHeight(),
      $carcass_back_material->getEntity(),
      $carcass_material->getPreserveGrain()
    );

    $door_frame_material = $this->project->getDoorFrameMaterial();
    $this->doorFrameStock = StockItem::createLoadItem(
      $door_frame_material->getName(),
      $this->project->getDoorFrameStockThickness(),
      $door_frame_material->getWidth(),
      $this->project->getDoorFrameHeight(),
      $door_frame_material->getEntity(),
      $door_frame_material->getPreserveGrain()
    );

    $door_panel_material = $this->project->getDoorPanelMaterial();
    $this->doorPanelStock = StockItem::createLoadItem(
      $door_panel_material->getName(),
      $door_panel_material->getDepth(),
      $door_panel_material->getWidth(),
      $door_panel_material->getHeight(),
      $door_panel_material->getEntity(),
      $carcass_material->getPreserveGrain()
    );
  }

  /**
   * Build the cabinet and determine the parts required.
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

    // Determine vertical division ratio array.
    $divisions_array = [];
    foreach ($this->module->getDivisionRatios() as $ratio_value) {
      if (!empty($ratio_value)) {
        $divisions_array[] = (float) $ratio_value;
      }
    }
    $this->divisions = $divisions_array;

    // Generate parts for cabinet.
    $this->generateParts();
  }

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

    if ($this->doorsAcrossGap > 0) {
      $this->generateDoorParts();
    }
  }

  /**
   * Set inner width of this cabinet.
   */
  protected function setInnerWidth() {
    $this->carcassInnerWidth = $this->width
     - (2 * $this->carcassStock->getDepth());
  }

  /**
   * Generate the shelves for this cabinet.
   */
  protected function generateShelfParts() {
    for ($shelf_id = 0; $shelf_id < $this->numShelves; $shelf_id++) {
      $this->parts[] = CabinetryPart::createPart(
        t(
          '[@module_name] Shelf @shelf_id',
            [
              '@module_name' => $this->module->getName(),
              '@shelf_id' => $shelf_id,
            ]
        ),
        $this->carcassStock->getDepth(),
        $this->carcassInnerWidth - CabinetProject::CABINET_PROJECT_CABINET_SHELF_UNDERSIZE,
        $this->depth - CabinetProject::CABINET_PROJECT_CABINET_SHELF_UNDERSIZE,
        $this->carcassStock,
        ''
      );
      $this->addBanding($this->carcassStock->getMaterial(), $this->carcassInnerWidth - CabinetProject::CABINET_PROJECT_CABINET_SHELF_UNDERSIZE);
    }
  }

  /**
   * Generate a side panel for the caracass.
   *
   * @param string $label
   *   A label to identify the panel (Left, Right).
   */
  protected function generateSidePart($label) {
    $sheet_depth = $this->carcassStock->getDepth();
    $dado_depth = round($sheet_depth / 2, 1);

    $this->parts[] = CabinetryPart::createPart(
      t(
        '[@module_name] @label Carcass',
        [
          '@label' => $label,
          '@module_name' => $this->module->getName(),
        ]
      ),
      $sheet_depth,
      $this->height,
      $this->depth,
      $this->carcassStock,
      "{$this->carcassBackStock->getDepth()}mm w|{$dado_depth}mm d dado {$sheet_depth}mm inset from long edge"
    );
    $this->addBanding($this->carcassStock->getMaterial(), $this->height);
  }

  /**
   * Generate a top or bottom panel for the carcass.
   *
   * @param string $label
   *   A label to identify the side (Top, Bottom).
   */
  protected function generateTopBottomPart($label) {
    $sheet_depth = $this->carcassStock->getDepth();

    $dado_depth = round($sheet_depth / 2, 1);

    $this->parts[] = CabinetryPart::createPart(
      t(
        '[@module_name] @label Carcass',
        [
          '@label' => $label,
          '@module_name' => $this->module->getName(),
        ]
      ),
      $sheet_depth,
      $this->carcassInnerWidth,
      $this->depth,
      $this->carcassStock,
      "{$this->carcassBackStock->getDepth()}mm w|{$dado_depth}mm d dado {$sheet_depth}mm inset from long edge"
    );
    $this->addBanding($this->carcassStock->getMaterial(), $this->carcassInnerWidth);
  }

  /**
   * Set inner width of this cabinet.
   */
  protected function generateTopSpreaders() {
    for ($spreader_id = 0; $spreader_id < 2; $spreader_id++) {
      $this->parts[] = CabinetryPart::createPart(
        t(
          '[@module_name] Top Spreader @spreader_id',
          [
            '@module_name' => $this->module->getName(),
            '@spreader_id' => $spreader_id,
          ]
        ),
        $this->carcassStock->getDepth(),
        $this->carcassInnerWidth,
        CabinetProject::CABINET_PROJECT_CABINET_NAILER_HEIGHT,
        $this->carcassStock,
        ''
      );
    }
  }

  /**
   * Generate the back panel parts for the cabinet caracass.
   */
  protected function generateBackPanelPart() {
    $sheet_depth = $this->carcassStock->getDepth();
    $dado_depth = round($sheet_depth * 0.40, 1);

    $this->parts[] = CabinetryPart::createPart(
      t(
        '[@module_name] Carcass Back',
        [
          '@module_name' => $this->module->getName(),
        ]
      ),
      $this->carcassBackStock->getDepth(),
      $this->height - (2 * $sheet_depth) + (2 * $dado_depth),
      $this->carcassInnerWidth + (2 * $dado_depth),
      $this->carcassBackStock,
      ''
    );
  }

  /**
   * Generate the divider panel parts for the cabinet caracass.
   */
  protected function generateDividerPanelParts() {
    for ($divider_id = 0; $divider_id < count($this->divisions) - 1; $divider_id++) {
      $this->generateTopBottomPart("Section $divider_id Divider");
    }
  }

  /**
   * Generate the nailer strips panel parts for the cabinet carcass.
   */
  protected function generateNailerParts() {
    for ($nailer_id = 0; $nailer_id < 2; $nailer_id++) {
      $this->parts[] = CabinetryPart::createPart(
        t(
          '[@module_name] Nailer @nailer_id',
          [
            '@module_name' => $this->module->getName(),
            '@nailer_id' => $nailer_id,
          ]
        ),
        $this->carcassStock->getDepth(),
        $this->carcassInnerWidth,
        CabinetProject::CABINET_PROJECT_CABINET_NAILER_HEIGHT,
        $this->carcassStock,
        'Two pocket holes on each end'
      );
    }
  }

  /**
   * Generate the doors for the cabinet.
   */
  protected function generateDoorParts() {
    $this->doors = [];
    $num_doors = $this->doorsAcrossGap;

    $num_divisions = count($this->divisions);
    foreach ($this->divisions as $division_index => $division_ratio) {
      for ($door_counter = 0; $door_counter < $this->doorsAcrossGap; $door_counter++) {
        $door = new CabinetDoor(
          t(
            '[@module_name] Div#@division_id Door @door_counter/@doors_total',
            [
              '@module_name' => $this->module->getName(),
              '@division_id' => $division_index + 1,
              '@door_counter' => $door_counter + 1,
              '@doors_total' => $num_doors,
            ]
          ),
          ($this->width - ((1 + $this->doorsAcrossGap) * $this->doorReveal)) / $this->doorsAcrossGap,
          ($this->height - ((1 + $num_divisions) * $this->doorReveal)) * $division_ratio,
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
  }

  /**
   * Plot a 2D image of this component.
   */
  public function plotModule($plotter) {
    $sheet_color = imagecolorallocate($plotter->canvas, 0, 0, 0);
    $door_reveal = $this->project->getDoorReveal();
    $frame_height = $this->doorFrameStock->getHeight();
    $doors_across_gap = $this->module->getDoorsAcrossGap();

    // Carcass box.
    imagerectangle(
      $plotter->canvas,
      0,
      0,
      $this->module->getWidth(),
      $this->module->getHeight(),
      $sheet_color
    );

    $num_divisions = count($this->module->getDivisionRatios());
    $door_width = ($this->module->getWidth() - (($doors_across_gap + 1) * $door_reveal)) / $doors_across_gap;
    $running_door_top = $this->module->getHeight();

    foreach ($this->module->getDivisionRatios() as $division_index => $division_ratio) {
      $door_height = ($this->module->getHeight() - (($num_divisions + 1) * $door_reveal)) * $division_ratio;
      $running_door_top = $running_door_top - $door_reveal;

      for ($door_counter = 0; $door_counter < $doors_across_gap; $door_counter++) {
        // Door outer.
        imagerectangle(
          $plotter->canvas,
          $door_reveal + $door_counter * ($door_reveal + $door_width),
          $running_door_top,
          $this->module->getWidth() - $door_reveal - $door_counter * ($door_reveal + $door_width),
          $running_door_top - $door_height,
          $sheet_color
        );

        // Door inner.
        imagerectangle(
          $plotter->canvas,
          $door_reveal + ($door_counter * $door_width) + $frame_height,
          $running_door_top - $frame_height,
          $this->module->getWidth() - $door_reveal - ((($doors_across_gap - 1) + -$door_counter) * $door_width) - $frame_height,
          $running_door_top - $door_height + $frame_height,
          $sheet_color
        );
      }

      $running_door_top = $running_door_top - $door_height;
    }
  }

}
