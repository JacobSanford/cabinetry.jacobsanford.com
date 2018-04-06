<?php

namespace Drupal\cabinetry_core\Cabinetry\SheetPlotter;

use Drupal\cabinetry_core\Cabinetry\CabinetryPlotter;
use Drupal\cabinetry_core\Cabinetry\SheetPacker\PackedSheet;
use Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF\ShelfFFShelf;

/**
 * A generic object for plotting a sheet good.
 */
class SheetPlotter extends CabinetryPlotter {

  /**
   * The sheet good to plot.
   *
   * @var \Drupal\cabinetry_core\Cabinetry\SheetPacker\PackedSheet
   */
  public $sheet = NULL;

  /**
   * Constructor.
   *
   * @param \Drupal\cabinetry_core\Cabinetry\SheetPacker\PackedSheet $sheet
   *   The PackedSheet sheet object to plot.
   */
  public function __construct(PackedSheet $sheet) {
    $this->sheet = $sheet;
    parent::__construct($sheet->width, $sheet->height);
  }

  /**
   * Plot the sheet to the canvas.
   */
  public function plotSheet() {
    $sheet_color = imagecolorallocate($this->canvas, 0, 0, 0);
    imagerectangle(
      $this->canvas,
      $this->padding + $this->labelOffset,
      $this->padding,
      $this->padding + $this->labelOffset + $this->width - 1,
      $this->height + $this->padding - 1,
      $sheet_color
    );

    foreach ($this->sheet->shelves as $shelf_index => $shelf) {
      $this->plotShelf($shelf);
      $this->shelfOffset += $shelf->height;
    }

    return $this->writeImage('cabinetry_sheet');
  }

  /**
   * Plot a shelf to the canvas.
   *
   * @param \Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF\ShelfFFShelf $shelf
   *   The CabinetryShelfFFShelf object to plot.
   */
  public function plotShelf(ShelfFFShelf $shelf) {
    $shelf_top_boundary = $this->padding + $this->height - $shelf->height - $this->shelfOffset;

    // Plot shelf pieces.
    $cur_x = $this->padding + $this->labelOffset;
    foreach ($shelf->parts as $piece_index => $piece) {
      /* @var $piece \Drupal\cabinetry_core\CabinetryPartInterface */
      if (!empty($piece->getWidth()) && $piece->getWidth() != 0 && !empty($piece->getHeight()) && $piece->getHeight() != 0) {
        $upper_left_x = $cur_x;
        $bottom_right_x = $cur_x + $piece->getWidth();
        $bottom_right_y = $shelf_top_boundary + $shelf->height;
        $upper_left_y = $bottom_right_y - $piece->getHeight();

        // Plot piece.
        imagefilledrectangle(
          $this->canvas,
          $upper_left_x,
          $upper_left_y,
          $bottom_right_x,
          $bottom_right_y,
          $this->partColor
        );
        imagerectangle(
          $this->canvas,
          $upper_left_x,
          $upper_left_y,
          $bottom_right_x,
          $bottom_right_y,
          $this->cutColor
        );

        $shelf_label_1 = $piece->getName();
        $shelf_label_2 = "{$piece->getWidth()}mm x {$piece->getHeight()}mm";

        // Plot piece label.
        $this->centerLabelsInShape(
          $shelf_label_1,
          $shelf_label_2,
          $upper_left_x,
          $upper_left_y,
          $bottom_right_x,
          $bottom_right_y,
          $piece->getRotatedValue()
        );
        $cur_x += $piece->getWidth();
      }

      // Shelf Boundary line.
      imageline(
        $this->canvas,
        $this->padding + $this->labelOffset - ($this->labelOffset / 4),
        $shelf_top_boundary,
        $this->padding + $this->labelOffset + $this->width,
        $shelf_top_boundary,
        $this->shelfColor
      );
      $this->fontSize = 5;
      imagestring(
        $this->canvas,
        $this->fontSize,
        $this->padding + $this->labelOffset / 2,
        $shelf_top_boundary,
        "{$shelf->height}mm",
        $this->labelColor
      );
    }
  }

}
