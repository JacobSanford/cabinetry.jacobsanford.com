<?php

namespace Drupal\cabinetry_core\Cabinetry\SheetPacker;

class PackedSheet {

  /**
   * The blade cut width to consider, in millimeters.
   *
   * @var float
   */
  public $cutWidth = 0.0;

  /**
   * The height (y) of the sheet, in millimeters.
   *
   * @var float
   */
  public $height = 0.0;

  /**
   * The width (x) of the sheet, in millimeters.
   *
   * @var float
   */
  public $width = 0.0;

}
