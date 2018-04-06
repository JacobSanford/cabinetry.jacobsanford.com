<?php

namespace Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF;

use Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF\ShelfFFShelf;
use Drupal\cabinetry_core\CabinetryPartInterface;

/**
 * An object providing a set of CabinetryShelfFFShelf shelves.
 */
class ShelfFFShelfSet {

  /**
   * The shelves in this shelf set.
   *
   * @var \Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF\ShelfFFShelf[]
   */
  public $shelves = [];

  /**
   * The width (x) of the shelf set, in millimeters.
   *
   * @var float
   */
  public $width = 0.0;

  /**
   * Can shelves rotate items to fit?
   *
   * @var bool
   */
  public $canRotate = FALSE;

  /**
   * The blade cut width to consider, in millimeters.
   *
   * @var float
   */
  public $cutWidth = 0.0;

  /**
   * Constructor.
   *
   * @param float $width
   *   The width of the shelf, in millimeters.
   * @param float $cut_width
   *   The blade cut width to allow for when packing the sheet.
   * @param bool $can_rotate
   *   Should the part orientations (grain parallel to width) be disregarded?
   */
  public function __construct($width, $cut_width, $can_rotate) {
    $this->width = $width;
    $this->canRotate = $can_rotate;
    $this->cutWidth = $cut_width;
  }

  /**
   * Add a part to the set of shelves.
   *
   * @param \Drupal\cabinetry_core\CabinetryPartInterface $part
   *   The part to add to the shelf.
   */
  public function addPart(CabinetryPartInterface $part) {
    $fit_ledger = [];

    // Look at all shelves and determine what adding the part will do.
    foreach ($this->shelves as $shelf_index => $shelf) {
      $shelf_remainder = $shelf->partFits($part);
      if ($shelf_remainder !== FALSE) {
        $fit_ledger[] = [
          'remain' => $shelf_remainder,
          'index' => $shelf_index,
          'rotated' => FALSE,
        ];
      }

      // If we can rotate this type, test it that way.
      if ($this->canRotate) {
        $shelf_remainder = $shelf->partFits($part, TRUE);
        if ($shelf_remainder !== FALSE) {
          $fit_ledger[] = [
            'remain' => $shelf_remainder,
            'index' => $shelf_index,
            'rotated' => TRUE,
          ];
        }
      }
    }

    if (!empty($fit_ledger)) {
      // Add this part to the shelf that will most efficiently use space.
      usort($fit_ledger, function ($item1, $item2) {
        if ($item1['remain'] == $item2['remain']) {
          return 0;
        }
        return $item1['remain'] < $item2['remain'] ? -1 : 1;
      });
      $best_fit = array_shift($fit_ledger);
      $this->shelves[$best_fit['index']]->addPart($part, $best_fit['rotated']);
    }
    else {
      // Create a new shelf.
      $this->addShelf($part);
    }

  }

  /**
   * Add a part to a new shelf, and add the shelf to the set of shelves.
   *
   * @param \Drupal\cabinetry_core\CabinetryPartInterface $part
   *   The part to add to the shelf.
   */
  public function addShelf(CabinetryPartInterface $part) {
    $this->shelves[] = new ShelfFFShelf(
      $part->getHeight(),
      $this->width,
      $this->cutWidth,
      $this->canRotate
    );
    $this->shelves[count($this->shelves) - 1]->addPart($part);
  }

}
