<?php

namespace Drupal\cabinetry_cabinet_project;

use Drupal\cabinetry_core\PhysicalObjectInterface;

/**
 * Provides an interface defining a Cabinet Module entity.
 *
 * @ingroup cabinetry
 */
interface CabinetModuleInterface extends PhysicalObjectInterface {

  /**
   * Gets the name of the class used to build the module.
   *
   * @return string
   *   The name of the class.
   */
  public function getClass();

  /**
   * Sets the name of the class used to build the module.
   *
   * @param string $class
   *   The name of the class.
   *
   * @return $this
   */
  public function setClass($class);

  /**
   * Gets the name of the class label.
   *
   * @return string
   *   The name of the class label.
   */
  public function getClassLabel();

  /**
   * Sets the name of the class label.
   *
   * @param string $label
   *   The name of the class label.
   *
   * @return $this
   */
  public function setClassLabel($label);

  /**
   * Gets if a counter is on top of the module.
   *
   * @return bool
   *   TRUE if a counter is on top of the module, FALSE otherwise.
   */
  public function getCounterOnTop();

  /**
   * Sets the number of shelves for the module.
   *
   * @param int $counter_on_top
   *   TRUE if a counter is on top of the module, FALSE otherwise.
   *
   * @return $this
   */
  public function setCounterOnTop($counter_on_top);

  /**
   * Gets the vertical division ratios for this module.
   *
   * @return float[]
   *   An array of values representing the vertical divisions of the module.
   */
  public function getDivisionRatios();

  /**
   * Sets the vertical division ratios for this module.
   *
   * @param array $divisions
   *   An array of float values representing vertical divisions of the module.
   *
   * @return $this
   */
  public function setDivisionRatios(array $divisions);

  /**
   * Gets the number of doors across the carcass opening.
   *
   * @return string
   *   The name of the class.
   */
  public function getDoorsAcrossGap();

  /**
   * Sets the number of doors across the carcass opening.
   *
   * @param int $doors_across_gap
   *   The number of doors across the carcass opening.
   *
   * @return $this
   */
  public function setDoorsAcrossGap($doors_across_gap);

  /**
   * Gets the name of the cabinet module.
   *
   * @return string
   *   The name of the cabinet module.
   */
  public function getName();

  /**
   * Sets the name of the cabinet module.
   *
   * @param string $name
   *   The name of the cabinet module.
   *
   * @return $this
   */
  public function setName($name);

  /**
   * Gets the number of shelves for the module.
   *
   * @return string
   *   The name of the class.
   */
  public function getNumShelves();

  /**
   * Sets the number of shelves for the module.
   *
   * @param int $num_shelves
   *   The number of shelves for the module.
   *
   * @return $this
   */
  public function setNumShelves($num_shelves);

  /**
   * Gets the parent project for this module.
   *
   * @return \Drupal\cabinetry_cabinet_project\CabinetProjectInterface
   *   The parent project.
   */
  public function getParentProject();

}
