<?php

namespace Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF;

use Drupal\cabinetry_core\CabinetryPartInterface;

/**
 * An object providing a sheet packing 'shelf'.
 */
class ShelfFFShelf {

  /**
   * The width (x) of the shelf, in millimeters.
   *
   * @var float
   */
  public $width = 0.0;

  /**
   * The height (y) of the shelf, in millimeters.
   *
   * @var float
   */
  public $height = 0.0;

  /**
   * The amount of the shelf width in use, in millimeters.
   *
   * @var float
   */
  public $used = 0.0;

  /**
   * The available width of the shelf, in millimeters.
   *
   * @var float
   */
  public $remain = 0.0;

  /**
   * Can the shelf rotate items to fit?
   *
   * @var bool
   */
  public $canRotate = FALSE;

  /**
   * An array of parts stored in the shelf.
   *
   * @var \Drupal\cabinetry_core\CabinetryPartInterface[]
   */
  public $parts = [];

  /**
   * The blade cut width to consider, in millimeters.
   *
   * @var float
   */
  public $cutWidth = 0.0;

  /**
   * Constructor.
   *
   * @param float $height
   *   The height of this shelf, in millimeters.
   * @param float $width
   *   The width of the shelf, in millimeters.
   * @param float $cut_width
   *   The blade cut width to allow for when packing the sheet.
   * @param bool $can_rotate
   *   Should the part orientations (grain parallel to width) be disregarded?
   */
  public function __construct($height, $width, $cut_width, $can_rotate) {
    $this->height = $height;
    $this->width = $width;
    $this->remain = $width;
    $this->cutWidth = $cut_width;
    $this->canRotate = $can_rotate;
  }

  /**
   * Add a part to the shelf.
   *
   * @param \Drupal\cabinetry_core\CabinetryPartInterface $part
   *   The part to add to the shelf.
   * @param bool $rotated
   *   TRUE if the part be rotated prior to being added, FALSE otherwise.
   */
  public function addPart(CabinetryPartInterface $part, $rotated = FALSE) {
    if ($rotated == TRUE) {
      $width_placeholder = $part->getWidth();
      $part->set('width', $part->getHeight());
      $part->set('height', $width_placeholder);
      $part->setRotatedValue(TRUE);
    }
    else {
      $part->setRotatedValue(FALSE);
    }

    if ($part->getWidth() > $this->width) {
      die(t('Part exceeds shelf length'));
    }

    $this->parts[] = $part;
    $this->setRemainder();
  }

  /**
   * Set the remainder and used properties.
   */
  public function setRemainder() {
    $sum = 0.0;
    foreach ($this->parts as $part) {
      $sum += $part->getWidth();
    }
    $this->used = $sum + (count($this->parts) - 1) * $this->cutWidth;
    $this->remain = $this->width - $this->used;
  }

  /**
   * Check if the part fits in this shelf.
   *
   * A scoring system based on consuming the most shelf height possible is used
   * to determine the best orientation of parts in the shelf.
   *
   * @param \Drupal\cabinetry_core\CabinetryPartInterface $part
   *   The part to add to the shelf.
   * @param bool $rotated
   *   TRUE if the part should be rotated before testing, FALSE otherwise.
   *
   * @return mixed
   *   The remainder of the shelf after fitting this part, FALSE otherwise.
   */
  public function partFits(CabinetryPartInterface $part, $rotated = FALSE) {
    if ($rotated) {
      $test_width = $part->getHeight();
      $test_height = $part->getWidth();
    }
    else {
      $test_width = $part->getWidth();
      $test_height = $part->getHeight();
    }

    if ($test_height > $this->height) {
      return FALSE;
    }

    if ($test_width > $this->remain) {
      return FALSE;
    }

    return $this->getRemain() - $test_width;
  }

  /**
   * Sort parts array by height DESC, then width DESC.
   */
  public function sortPartsByHeightAndWidth() {
    $sort = [];
    foreach ($this->parts as $k => $v) {
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
   * Sort parts array by height DESC, then width DESC.
   */
  public function getRemain() {
    return $this->remain;
  }

  /**
   * Sort parts array by height DESC, then width DESC.
   */
  public function getHeight() {
    return $this->height;
  }

}
