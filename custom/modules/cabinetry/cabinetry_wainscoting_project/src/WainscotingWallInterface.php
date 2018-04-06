<?php

namespace Drupal\cabinetry_wainscoting_project;

use Drupal\cabinetry_core\PhysicalObjectInterface;

/**
 * Provides an interface defining a Wainscoting Wall entity.
 *
 * @ingroup cabinetry
 */
interface WainscotingWallInterface extends PhysicalObjectInterface {

  /**
   * Gets the name of the wainscoting wall.
   *
   * @return string
   *   The name of the wainscoting wall.
   */
  public function getName();

  /**
   * Sets the name of the wainscoting wall.
   *
   * @param string $name
   *   The name of the wainscoting wall.
   *
   * @return $this
   */
  public function setName($name);

  /**
   * Gets the parent project for this wall.
   *
   * @return \Drupal\cabinetry_wainscoting_project\CWainscotingProjectInterface
   *   The parent project.
   */
  public function getParentProject();

}
