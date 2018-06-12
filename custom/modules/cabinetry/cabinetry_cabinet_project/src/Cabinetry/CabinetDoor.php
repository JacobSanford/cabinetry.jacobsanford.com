<?php

namespace Drupal\cabinetry_cabinet_project\Cabinetry;

use Drupal\cabinetry_cabinet_project\Cabinetry\CabinetComponent;
use Drupal\cabinetry_core\Entity\CabinetryPart;
use Drupal\cabinetry_core\StockItemInterface;

/**
 * A generic object to serve as edge banding type in a cabinetry project.
 */
class CabinetDoor extends CabinetComponent {

  /**
   * The door frame material.
   *
   * @var \Drupal\cabinetry_core\StockItemInterface
   */
  public $doorFrameStock = NULL;

  /**
   * The door frame height.
   *
   * @var float
   */
  public $doorFrameHeight = NULL;

  /**
   * The door frame thickness.
   *
   * @var float
   */
  public $doorFrameThickness = NULL;

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
   * The door panel undersize for expansion, each side, in millimeters.
   *
   * @var float
   */
  public $doorPanelUndersize = 0.0;

  /**
   * A label identifying this door within the project.
   *
   * @var string
   */
  public $label;

  /**
   * Constructor.
   *
   * @param string $label
   *   A label identifying this door within the project.
   * @param float $width
   *   The width of this sheet good, in millimeters.
   * @param float $height
   *   The height of this sheet good, in millimeters.
   * @param \Drupal\cabinetry_core\StockItemInterface $door_frame_stock
   *   The door frame material, type CabinetryWoodPiece.
   * @param float $door_frame_height
   *   The height of the door frame in in millimeters.
   * @param float $door_frame_thickness
   *   The thickness of the door frame in in millimeters.
   * @param object $door_frame_router_bit
   *   The door frame router bit, type CabinetryToolItem.
   * @param \Drupal\cabinetry_core\StockItemInterface $door_panel_stock
   *   The door panel material, type CabinetryWoodPiece.
   * @param float $door_panel_undersize
   *   The amount to undersize the door panel to allow for expansion, each
   *   side, in millimeters.
   */
  public function __construct($label, $width, $height, StockItemInterface $door_frame_stock, $door_frame_height, $door_frame_thickness, $door_frame_router_bit, StockItemInterface $door_panel_stock, $door_panel_undersize) {
    $this->label = $label;
    $this->width = $width;
    $this->height = $height;
    $this->doorFrameStock = $door_frame_stock;
    $this->doorFrameRouterBit = $door_frame_router_bit;
    $this->doorPanelStock = $door_panel_stock;
    $this->doorFrameHeight = $door_frame_height;
    $this->doorFrameThickness = $door_frame_thickness;
    $this->doorPanelUndersize = $door_panel_undersize;
    $this->generateParts();
  }

  /**
   * Generate parts required to build this door.
   */
  public function generateParts() {
    $this->parts = [];
    $this->generateStilePart(t('Left'));
    $this->generateStilePart(t('Right'));
    $this->generateRailPart(t('Top'));
    $this->generateRailPart(t('Bottom'));
    $this->generatePanelPart();
  }

  /**
   * Generate the stile components for this door.
   *
   * @param string $label
   *   A label to identify the side (Left, Right).
   */
  protected function generateStilePart($label) {
    $this->parts[] = CabinetryPart::createPart(
      t(
        '@label @direction Stile',
        [
          '@direction' => $label,
          '@height' => $this->doorFrameHeight,
          '@label' => $this->label,
        ]
      ),
      $this->doorFrameThickness,
      $this->height,
      $this->doorFrameHeight,
      $this->doorFrameStock,
      ''
    );
  }

  /**
   * Generate the rail components for this door.
   *
   * @param string $label
   *   A label to identify the side (Top, Bottom).
   */
  protected function generateRailPart($label) {
    $this->parts[] = CabinetryPart::createPart(
      t(
        '@label @direction Rail',
        [
          '@direction' => $label,
          '@height' => $this->doorFrameHeight,
          '@label' => $this->label,
        ]
      ),
      $this->doorFrameThickness,
      $this->width - 2
        * $this->doorFrameHeight + 2
        * $this->doorFrameRouterBit->getCutDepth(),
      $this->doorFrameHeight,
      $this->doorFrameStock,
      ''
    );
  }

  /**
   * Generate the panel component for this door.
   */
  protected function generatePanelPart() {
    $this->parts[] = CabinetryPart::createPart(
      t(
        '@label Door Panel',
        [
          '@label' => $this->label,
        ]
      ),
      $this->doorPanelStock->getDepth(),
        $this->height - 2
        * $this->doorFrameHeight + 2
        * $this->doorFrameRouterBit->getCutDepth() - 2
        * $this->doorPanelUndersize,
      $this->width - 2
        * $this->doorFrameHeight + 2
        * $this->doorFrameRouterBit->getCutDepth() - 2
        * $this->doorPanelUndersize,
      $this->doorPanelStock,
      ''
    );
  }

}
