<?php

namespace Drupal\cabinetry_core;

use Drupal\cabinetry_core\PhysicalObjectInterface;
use Drupal\cabinetry_core\StockItemInterface;

/**
 * Provides an interface defining a Cabinet Part entity.
 *
 * @ingroup cabinetry
 */
interface CabinetryPartInterface extends PhysicalObjectInterface {

  /**
   * Factory for building a cabinetry part.
   *
   * @param string $name
   *   The name of the part.
   * @param float $depth
   *   The depth of the part, in millimeters.
   * @param float $width
   *   The width of the part, in millimeters.
   * @param float $height
   *   The height of the part, in millimeters.
   * @param \Drupal\cabinetry_core\StockItemInterface $stock_source
   *   The material of the part.
   * @param string $notes
   *   Any notes related to this part.
   * @param bool $save
   *   TRUE if the part be persistently saved before return.
   *
   * @return \Drupal\cabinetry_core\CabinetryPartInterface
   *   The newly created entity.
   */
  public static function createPart($name, $depth, $width, $height, StockItemInterface $stock_source, $notes, $save = TRUE);

  /**
   * Gets the stock source of the part.
   *
   * @return \Drupal\cabinetry_core\StockItemInterface
   *   The stock item source of the part.
   */
  public function getStockSource();

  /**
   * Sets the stock source of the part.
   *
   * @param \Drupal\cabinetry_core\StockItemInterface $material
   *   The stock source term.
   *
   * @return $this
   */
  public function setStockSource(StockItemInterface $material);

  /**
   * Gets the stock source tid of the part.
   *
   * @return int
   *   The term tid of the stock source.
   */
  public function getStockSourceId();

  /**
   * Sets the stock source tid of the part.
   *
   * @param int $tid
   *   The tid of the material term.
   *
   * @return $this
   */
  public function setStockSourceId($tid);

  /**
   * Gets the name of the cabinetry part.
   *
   * @return string
   *   The name of the cabinetry part.
   */
  public function getName();

  /**
   * Sets the name of the cabinetry part.
   *
   * @param string $name
   *   The name of the cabinetry part.
   *
   * @return $this
   */
  public function setName($name);

  /**
   * Gets the notes for the cabinetry part.
   *
   * @return string
   *   The notes for the cabinetry part.
   */
  public function getNotes();

  /**
   * Sets the notes for the cabinetry part.
   *
   * @param string $notes
   *   The notes for the cabinetry part.
   *
   * @return $this
   */
  public function setNotes($notes);

  /**
   * Gets the rotated status of the part.
   *
   * @return bool
   *   TRUE if the part is rotated, FALSE otherwise.
   */
  public function getRotatedValue();

  /**
   * Sets the rotated status of the part.
   *
   * @param bool $rotated
   *   TRUE if the part is rotated, FALSE otherwise.
   *
   * @return $this
   */
  public function setRotatedValue($rotated);

}
