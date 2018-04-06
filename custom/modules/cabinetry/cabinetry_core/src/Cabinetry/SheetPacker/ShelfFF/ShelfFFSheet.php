<?php

namespace Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF;

use Drupal\cabinetry_core\Cabinetry\SheetPacker\PackedSheet;

/**
 * An object providing a sheet packing sheet.
 */
class ShelfFFSheet extends PackedSheet {

  /**
   * The used height of sheet good by shelves, in millimeters.
   *
   * @var float
   */
  public $used = 0.0;

  /**
   * The unused height of sheet good, in millimeters.
   *
   * @var float
   */
  public $remain = 0.0;

  /**
   * An array of CabinetryShelfFFShelf objects assigned to this sheet.
   *
   * @var \Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF\ShelfFFShelf[]
   */
  public $shelves = [];

  /**
   * Constructor.
   *
   * @param float $height
   *   The height of this sheet, in millimeters.
   * @param float $width
   *   The width of the shelf, in millimeters.
   * @param float $cut_width
   *   The blade cut width to allow for when packing the sheet.
   */
  public function __construct($height, $width, $cut_width) {
    $this->height = $height;
    $this->width = $width;
    $this->remain = $height;
    $this->cutWidth = $cut_width;
  }

  /**
   * Add a shelf to the sheet.
   *
   * @param \Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF\ShelfFFShelf $shelf
   *   The shelf to add to the sheet.
   */
  public function addShelf(ShelfFFShelf $shelf) {
    $this->shelves[] = $shelf;
    $this->setRemainder();
  }

  /**
   * Set the remainder and used properties.
   */
  public function setRemainder() {
    $sum = 0.0;
    foreach ($this->shelves as $shelf) {
      $sum += $shelf->height;
    }
    $this->used = $sum + (count($this->shelves) - 1) * $this->cutWidth;
    $this->remain = $this->height - $this->used;
  }

  /**
   * Check if the shelf fits in this sheet.
   *
   * @param \Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF\ShelfFFShelf $shelf
   *   The shelf to check if it fits in the sheet.
   *
   * @return bool
   *   TRUE if the shelf fits, FALSE otherwise.
   */
  public function shelfFits(ShelfFFShelf $shelf) {
    if ($shelf->height > $this->height) {
      return FALSE;
    }

    if ($shelf->height > $this->remain) {
      return FALSE;
    }

    return $this->remain - $shelf->height;
  }

}
