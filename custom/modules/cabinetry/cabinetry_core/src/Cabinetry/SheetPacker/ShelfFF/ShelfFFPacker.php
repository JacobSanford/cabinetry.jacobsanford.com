<?php

namespace Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF;

use Drupal\cabinetry_core\Cabinetry\SheetPacker\SheetPacker;
use Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF\ShelfFFSheet;
use Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF\ShelfFFShelfSet;
use Drupal\cabinetry_core\CabinetryProjectInterface;
use Drupal\cabinetry_core\StockItemInterface;

/**
 * A rudimentary object for packing a 2D sheet good from a list of parts.
 *
 * The sheet layout is calculated with a modified 2D bin packing algorithm,
 * based upon https://github.com/juj/RectangleBinPack as an example. The base
 * algorithm used is the SHELF-FF, with a modification that considers cabinet
 * doors look best with a vertical grain orientation.
 *
 * The algorithm isn't 100% efficient with intent of producing layouts that
 * ease the burden of cutting the sheets with a track (circular) saw. A 'shelf'
 * layout provides straight lines that are easy to break down quickly, while
 * minimizing human error. This is implemented by pre-sorting the parts from
 * largest to smallest before packing the shelves.
 *
 * Those planning to adapt this to set up a cut list (and toolpath) for a CNC
 * machine: CNC changes the above layout consideration significantly. Guillotine
 * based algorithms are significantly more efficient and should be considered,
 * since there is limited human involvement. If you do adapt this, please let
 * me know / contribute!
 */
class ShelfFFPacker extends SheetPacker {

  /**
   * An array of CabinetryShelfFFShelf objects.
   *
   * @var \Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF\ShelfFFShelf[]
   */
  public $shelves = [];

  /**
   * Constructor.
   *
   * @param \Drupal\cabinetry_core\CabinetryProjectInterface $project
   *   The project to store the sheets in.
   * @param \Drupal\cabinetry_core\StockItemInterface $material
   *   The material of the sheet.
   * @param \Drupal\cabinetry_core\CabinetryPartInterface[] $parts
   *   Parts to pack into sheets.
   */
  protected function __construct(CabinetryProjectInterface $project, StockItemInterface $material, array $parts) {
    parent::__construct($project, $material, $parts);
    $this->sortPartsByHeightAndWidth();
    $this->pack();
  }

  /**
   * Sort parts array by height DESC, then width DESC.
   */
  protected function sortPartsByHeightAndWidth() {
    $sort = [];
    foreach ($this->parts as $k => $v) {
      /* @var $v \Drupal\cabinetry_core\CabinetryPartInterface */
      $sort['height'][$k] = $v->getHeight();
      $sort['width'][$k] = $v->getWidth();
    }
    array_multisort(
      $sort['height'],
      SORT_DESC,
      $sort['width'],
      SORT_DESC,
      $this->parts
    );
  }

  /**
   * Pack the parts into shelves, then shelves into sheets.
   */
  public function pack() {
    $this->packShelves();
    $this->packSheets();
  }

  /**
   * Pack the parts into shelves.
   */
  public function packShelves() {
    $sheet_set = new ShelfFFShelfSet(
      $this->width,
      $this->cutWidth,
      !$this->hasGrain
    );
    foreach ($this->parts as $part_index => $part) {
      $sheet_set->addPart($part);
    }
    $this->shelves = $sheet_set->shelves;
  }

  /**
   * Pack the shelves into sheets.
   */
  public function packSheets() {
    $this->sortShelvesByHeight();

    foreach ($this->shelves as $shelf) {
      /* @var $shelf \Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF\ShelfFFShelf */
      $shelf->sortPartsByHeightAndWidth();
      $sheet_found = FALSE;

      $fit_ledger = [];
      foreach ($this->sheets as $sheet_index => $sheet) {
        /* @var $sheet \Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF\ShelfFFSheet */
        $sheet_remainder = $sheet->shelfFits($shelf);
        if ($sheet_remainder !== FALSE) {
          $fit_ledger[] = [
            'remain' => $sheet_remainder,
            'index' => $sheet_index,
          ];
        }
      }

      if (!empty($fit_ledger)) {
        usort($fit_ledger, function ($item1, $item2) {
          if ($item1['remain'] == $item2['remain']) {
            return 0;
          }
          return $item1['remain'] < $item2['remain'] ? -1 : 1;
        });
        $best_fit = array_shift($fit_ledger);
        $this->sheets[$best_fit['index']]->addShelf($shelf);
      }
      else {
        $this->sheets[] = new ShelfFFSheet(
          $this->height,
          $this->width,
          $this->cutWidth
        );
        $this->sheets[count($this->sheets) - 1]->addShelf($shelf);
      }

    }
  }

  /**
   * Sort shelves array by height DESC.
   */
  public function sortShelvesByHeight() {
    usort($this->shelves, function ($item1, $item2) {
      if ($item1->getHeight() == $item2->getHeight()) {
        return 0;
      }
      return $item2->getHeight() < $item1->getHeight() ? -1 : 1;
    });
  }

}
