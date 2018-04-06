<?php

namespace Drupal\cabinetry_core;

use Drupal\cabinetry_core\PhysicalObjectInterface;
use Drupal\taxonomy\TermInterface;

/**
 * Provides an interface defining a PhysicalObject entity.
 *
 * @ingroup cabinetry
 */
interface StockItemInterface extends PhysicalObjectInterface {

  /**
   * Gets the name of the stock item.
   *
   * @return string
   *   The name of the cabinet module.
   */
  public function getName();

  /**
   * Sets the name of the stock item.
   *
   * @param string $name
   *   The name of the cabinetry part.
   *
   * @return $this
   */
  public function setName($name);

  /**
   * Gets the material object of the part.
   *
   * @return \Drupal\taxonomy\TermInterface
   *   A term representing the material of the sheet.
   */
  public function getMaterial();

  /**
   * Sets the material of the part.
   *
   * @param \Drupal\taxonomy\TermInterface $material
   *   The material term.
   *
   * @return $this
   */
  public function setMaterial(TermInterface $material);

  /**
   * Gets the material tid of the part.
   *
   * @return int
   *   The term tid of the material.
   */
  public function getMaterialId();

  /**
   * Sets the material tid of the part.
   *
   * @param int $tid
   *   The tid of the material term.
   *
   * @return $this
   */
  public function setMaterialId($tid);

  /**
   * Gets if the stock grain direction should be preserved.
   *
   * @return bool
   *   TRUE if the grain should be preserved, false otherwise.
   */
  public function getPreserveGrain();

}
