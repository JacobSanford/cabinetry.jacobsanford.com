<?php

namespace Drupal\cabinetry_cabinet_project;

use Drupal\cabinetry_cabinet_project\CabinetModuleInterface;
use Drupal\cabinetry_core\CabinetryProjectInterface;
use Drupal\taxonomy\TermInterface;

/**
 * Provides an interface defining a Cabinet Project entity.
 *
 * @ingroup cabinetry
 */
interface CabinetProjectInterface extends CabinetryProjectInterface {

  /**
   * Build the parts and components for a specific module in a project.
   *
   * @param int $project_eid
   *   The entity ID of the project.
   * @param int $module_eid
   *   The entity ID of the cabinet module.
   * @param array $context
   *   The optional batch context for Batch API.
   *
   * @return $this
   */
  public static function buildModuleBatch($project_eid, $module_eid, array &$context = []);

  /**
   * Gets the value of the door panel undersize.
   *
   * @return float
   *   The value of the door panel undersize.
   */
  public function getDoorPanelUndersize();

  /**
   * Sets the value of the door panel undersize.
   *
   * @param float $undersize
   *   The undersize of the door panel.
   *
   * @return $this
   */
  public function setDoorPanelUndersize($undersize);

  /**
   * Gets the value of the door reveal relative to the carcass.
   *
   * @return float
   *   The value of the door reveal relative to the carcass.
   */
  public function getDoorReveal();

  /**
   * Sets the value of the door reveal relative to the carcass.
   *
   * @param float $reveal
   *   The value of the door reveal.
   *
   * @return $this
   */
  public function setDoorReveal($reveal);

  /**
   * Gets the door frame material object.
   *
   * @return \Drupal\cabinetry_core\Cabinetry\SolidStockTypeTerm
   *   A term representing the door frame material.
   */
  public function getDoorFrameMaterial();

  /**
   * Sets the door frame material.
   *
   * @param \Drupal\taxonomy\TermInterface $material
   *   The material term.
   *
   * @return $this
   */
  public function setDoorFrameMaterial(TermInterface $material);

  /**
   * Gets the material tid of the door frame.
   *
   * @return int
   *   The term tid of the material.
   */
  public function getDoorFrameMaterialId();

  /**
   * Sets the material tid of the door frame.
   *
   * @param int $tid
   *   The tid of the material term.
   *
   * @return $this
   */
  public function setDoorFrameMaterialId($tid);

  /**
   * Gets the carcass back material object.
   *
   * @return \Drupal\cabinetry_core\Cabinetry\SheetStockTypeTerm
   *   A term representing the carcass back material.
   */
  public function getCarcassBackMaterial();

  /**
   * Sets the carcass back material object.
   *
   * @param \Drupal\taxonomy\TermInterface $material
   *   The material term.
   *
   * @return $this
   */
  public function setCarcassBackMaterial(TermInterface $material);

  /**
   * Gets the material tid of the carcass back.
   *
   * @return int
   *   The term tid of the material.
   */
  public function getCarcassBackMaterialId();

  /**
   * Sets the material tid of the carcass back.
   *
   * @param int $tid
   *   The tid of the material term.
   *
   * @return $this
   */
  public function setCarcassBackMaterialId($tid);

  /**
   * Gets the carcass sides material object.
   *
   * @return \Drupal\cabinetry_core\Cabinetry\SheetStockTypeTerm
   *   An object representing the carcass sides material.
   */
  public function getCarcassMaterial();

  /**
   * Sets the carcass sides material.
   *
   * @param \Drupal\taxonomy\TermInterface $material
   *   The material term.
   *
   * @return $this
   */
  public function setCarcassMaterial(TermInterface $material);

  /**
   * Gets the material tid of the carcass sides.
   *
   * @return int
   *   The term tid of the material.
   */
  public function getCarcassMaterialId();

  /**
   * Sets the material tid of the carcass sides.
   *
   * @param int $tid
   *   The tid of the material term.
   *
   * @return $this
   */
  public function setCarcassMaterialId($tid);

  /**
   * Gets the door panel material object.
   *
   * @return \Drupal\cabinetry_core\Cabinetry\SheetStockTypeTerm
   *   A term representing the door panel material.
   */
  public function getDoorPanelMaterial();

  /**
   * Sets the door panel material.
   *
   * @param \Drupal\taxonomy\TermInterface $material
   *   The material term.
   *
   * @return $this
   */
  public function setDoorPanelMaterial(TermInterface $material);

  /**
   * Gets the material tid of the door panel.
   *
   * @return int
   *   The term tid of the door panel.
   */
  public function getDoorPanelMaterialId();

  /**
   * Sets the material tid of the door panel.
   *
   * @param int $tid
   *   The tid of the material term.
   *
   * @return $this
   */
  public function setDoorPanelMaterialId($tid);

  /**
   * Gets the door panel router bit object.
   *
   * @return \Drupal\taxonomy\TermInterface
   *   A term representing the door panel router bit.
   */
  public function getDoorRouterBit();

  /**
   * Sets the door panel router bit.
   *
   * @param \Drupal\taxonomy\TermInterface $material
   *   The router bit term.
   *
   * @return $this
   */
  public function setDoorRouterBit(TermInterface $material);

  /**
   * Gets the router bit tid of the door panel.
   *
   * @return int
   *   The term tid of the door panel router bit.
   */
  public function getDoorRouterBitId();

  /**
   * Sets the router bit tid of the door panel.
   *
   * @param int $tid
   *   The tid of the router bit term.
   *
   * @return $this
   */
  public function setDoorRouterBitId($tid);

  /**
   * Gets if the project should be audited under the 32mm system.
   *
   * @return bool
   *   TRUE if the project should be audited under the 32mm system.
   */
  public function getIsThirtyTwoSystem();

  /**
   * Sets if the project should be audited under the 32mm system.
   *
   * @param bool $is_thirty_two
   *   TRUE if the project should be audited under the 32mm system.
   *
   * @return $this
   */
  public function setIsThirtyTwoSystem($is_thirty_two);

  /**
   * Gets the primary hinge object.
   *
   * @return \Drupal\taxonomy\TermInterface
   *   A term representing the hinge.
   */
  public function getPrimaryHinge();

  /**
   * Sets the primary hinge.
   *
   * @param \Drupal\taxonomy\TermInterface $material
   *   The primary hinge.
   *
   * @return $this
   */
  public function setPrimaryHinge(TermInterface $material);

  /**
   * Gets the primary hinge.
   *
   * @return int
   *   The term tid of the primary hinge.
   */
  public function getPrimaryHingeId();

  /**
   * Sets the tid of the primary hinge.
   *
   * @param int $tid
   *   The tid of the primary hinge term.
   *
   * @return $this
   */
  public function setPrimaryHingeId($tid);

  /**
   * Gets the primary hinge plate object.
   *
   * @return \Drupal\taxonomy\TermInterface
   *   A term representing the hinge plate.
   */
  public function getPrimaryHingePlate();

  /**
   * Sets the primary hinge plate.
   *
   * @param \Drupal\taxonomy\TermInterface $material
   *   The primary hinge plate.
   *
   * @return $this
   */
  public function setPrimaryHingePlate(TermInterface $material);

  /**
   * Gets the primary hinge plate.
   *
   * @return int
   *   The term tid of the primary hinge plate.
   */
  public function getPrimaryHingePlateId();

  /**
   * Sets the tid of the primary hinge plate.
   *
   * @param int $tid
   *   The tid of the primary hinge plate term.
   *
   * @return $this
   */
  public function setPrimaryHingePlateId($tid);

  /**
   * Gets the value of the door frame height.
   *
   * @return float
   *   The value of the door frame height.
   */
  public function getDoorFrameHeight();

  /**
   * Sets the value of the door frame height.
   *
   * @param float $height
   *   The value of the door frame height.
   *
   * @return $this
   */
  public function setDoorFrameHeight($height);

  /**
   * Gets the value of the door frame stock thickness.
   *
   * @return float
   *   The value of the door frame stock thickness.
   */
  public function getDoorFrameStockThickness();

  /**
   * Sets the value of the door frame stock thickness.
   *
   * @param float $thickness
   *   The value of the door frame stock thickness.
   *
   * @return $this
   */
  public function setDoorFrameStockThickness($thickness);

  /**
   * Gets the modules associated with this project.
   *
   * @return \Drupal\cabinetry_cabinet_project\CabinetModuleInterface[]
   *   An array of CabinetModuleInterface modules.
   */
  public function getCabinetModules();

  /**
   * Remove a cabinet module from this project.
   *
   * @param \Drupal\cabinetry_cabinet_project\CabinetModuleInterface[ $module
   *   The CabinetModuleInterface module to remove.
   *
   * @return $this
   */
  public function removeCabinetModule($module);

  /**
   * Gets the entity IDs of the cabinet modules associated with this project.
   *
   * @return int[]
   *   An array of entity IDs.
   */
  public function getCabinetModuleIds();

  /**
   * Set the modules associated with this project.
   *
   * @param \Drupal\cabinetry_cabinet_project\CabinetModuleInterface[] $modules
   *   An array of CabinetModuleInterface modules.
   *
   * @return $this
   */
  public function setCabinetModules(array $modules);

  /**
   * Check if a module is associated with this project.
   *
   * @param \Drupal\cabinetry_cabinet_project\CabinetModuleInterface $module
   *   An CabinetModuleInterface module.
   *
   * @return bool
   *   TRUE if the module is associated with this project. FALSE otherwise.
   */
  public function hasCabinetModule(CabinetModuleInterface $module);

  /**
   * Set the modules associated with this project.
   *
   * @param \Drupal\cabinetry_cabinet_project\CabinetModuleInterface $module
   *   An CabinetModuleInterface module.
   *
   * @return $this
   */
  public function addCabinetModule(CabinetModuleInterface $module);

  /**
   * Clear and then build parts and components for all modules.
   */
  public function batchRebuildParts();

  /**
   * Build parts and components for all modules.
   */
  public function batchBuildModules();

  /**
   * Pack sheets from parts for the project.
   */
  public function batchPackSheets();

}
