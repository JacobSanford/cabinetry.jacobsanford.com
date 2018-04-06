<?php

namespace Drupal\cabinetry_core;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a PhysicalObject entity.
 *
 * @ingroup cabinetry
 */
interface PhysicalObjectInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
   * Gets the depth of the object.
   *
   * @return float
   *   The depth of the object, in millimeters.
   */
  public function getDepth();

  /**
   * Sets the depth of the object.
   *
   * @param float $depth
   *   The depth of the object, in millimeters.
   *
   * @return $this
   */
  public function setDepth($depth);

  /**
   * Gets the height of the object.
   *
   * @return float
   *   The height of the object, in millimeters.
   */
  public function getHeight();

  /**
   * Sets the height of the object.
   *
   * @param float $height
   *   The height of the object, in millimeters.
   *
   * @return $this
   */
  public function setHeight($height);

  /**
   * Gets the width of the object.
   *
   * @return float
   *   The width of the object, in millimeters.
   */
  public function getWidth();

  /**
   * Sets the width of the object.
   *
   * @param float $width
   *   The width of the object, in millimeters.
   *
   * @return $this
   */
  public function setWidth($width);

}
