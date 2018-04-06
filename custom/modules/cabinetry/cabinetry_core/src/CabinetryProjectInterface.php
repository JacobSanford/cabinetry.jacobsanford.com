<?php

namespace Drupal\cabinetry_core;

use Drupal\cabinetry_core\CabinetryPartInterface;
use Drupal\cabinetry_core\CutSheetInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a Cabinetry Project entity.
 *
 * @ingroup cabinetry
 */
interface CabinetryProjectInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
   * Remove the cut sheets from a project, optionally in batch API.
   *
   * @param int $project_eid
   *   The entity ID of the project.
   * @param array $context
   *   The optional batch context for Batch API.
   */
  public static function removeCutSheetsBatch($project_eid, array &$context = []);

  /**
   * Remove the parts from a project, optionally in batch API.
   *
   * @param int $project_eid
   *   The entity ID of the project.
   * @param array $context
   *   The optional batch context for Batch API.
   */
  public static function removePartsBatch($project_eid, array &$context = []);

  /**
   * Clear current project parts, optionally in a batch operation.
   *
   * @param int $eid
   *   The entity ID of the project to clear.
   * @param string $entity_type
   *   The entity type to clear.
   * @param array $context
   *   The optional batch context for Batch API.
   */
  public static function clearProjectParts($eid, $entity_type, array &$context = []);

  /**
   * Clear current project sheets, optionally in a batch operation.
   *
   * @param int $eid
   *   The entity ID of the project to clear.
   * @param string $entity_type
   *   The entity type to clear.
   * @param array $context
   *   The optional batch context for Batch API.
   */
  public static function clearProjectCutSheets($eid, $entity_type = NULL, array &$context = []);

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
   * Gets the ID values of the cut sheet entities.
   *
   * @return int[]
   *   The IDs of the cut sheets attached to this project.
   */
  public function getCutSheetsIds();

  /**
   * Gets the cut sheet entities.
   *
   * @return \Drupal\cabinetry_core\CutSheetInterface[]
   *   The cut sheets attached to this project.
   */
  public function getCutSheets();

  /**
   * Sets the cut sheet entities.
   *
   * @param \Drupal\cabinetry_core\CutSheetInterface[] $cut_sheets
   *   The cut sheets for this project.
   *
   * @return $this
   */
  public function setCutSheets(array $cut_sheets);

  /**
   * Add a cut sheet to the project.
   *
   * @param \Drupal\cabinetry_core\CutSheetInterface $cut_sheet
   *   The cut sheet to add.
   *
   * @return $this
   */
  public function addCutSheet(CutSheetInterface $cut_sheet);

  /**
   * Clear the cut sheets from this project.
   *
   * @param bool $delete_entities
   *   TRUE if the cut sheet entities should be themselves deleted.
   *
   * @return $this
   */
  public function clearCutSheets($delete_entities = TRUE);

  /**
   * Gets the sheet part entities.
   *
   * @return \Drupal\cabinetry_core\CabinetryPartInterface[]
   *   The sheet parts for the project.
   */
  public function getParts();

  /**
   * Sets the sheet part entities.
   *
   * @param \Drupal\cabinetry_core\CabinetryPartInterface[] $sheet_parts
   *   The sheet parts for the project.
   *
   * @return $this
   */
  public function setParts(array $sheet_parts);

  /**
   * Gets the ID values of the project parts.
   *
   * @return int[]
   *   The IDs of the cut parts attached to this project.
   */
  public function getPartIds();

  /**
   * Add a sheet part to the project.
   *
   * @param \Drupal\cabinetry_core\CabinetryPartInterface $sheet_part
   *   The sheet part to add.
   *
   * @return $this
   */
  public function addPart(CabinetryPartInterface $sheet_part);

  /**
   * Clear the parts from this project.
   *
   * @param bool $delete_entities
   *   TRUE if the part entities should be themselves deleted, FALSE otherwise.
   *
   * @return $this
   */
  public function clearParts($delete_entities = TRUE);

  /**
   * Gets the saw blade for the project.
   *
   * @return \Drupal\taxonomy\TermInterface
   *   The blade used to cut wood for the project.
   */
  public function getSawBlade();

  /**
   * Sets the saw blade for the project.
   *
   * @param \Drupal\taxonomy\TermInterface $saw_blade
   *   The blade used to cut wood for the project.
   *
   * @return $this
   */
  public function setSawBlade(TermInterface $saw_blade);

  /**
   * Remove all built component entities from this project.
   */
  public function batchClearComponents();

  /**
   * Gets the saw blade cut width for the project.
   *
   * @return float
   *   The blade cut width, in millimeters.
   */
  public function getSawBladeCutWidth();

  /**
   * Gets the hardware items for the project.
   *
   * @return \Drupal\taxonomy\TermInterface[]
   *   The hardware items for the project.
   */
  public function getHardwareItems();

  /**
   * Sets the hardware items for the project.
   *
   * @param \Drupal\taxonomy\TermInterface[] $hardware_items
   *   The hardware items for the project.
   *
   * @return $this
   */
  public function setHardwareItems(array $hardware_items);

  /**
   * Clear the hardware items for the project.
   *
   * @return $this
   */
  public function clearHardwareItems();

  /**
   * Gets the ID values of the hardware item entities.
   *
   * @return int[]
   *   The IDs of the hardware items attached to this project.
   */
  public function getHardwareItemIds();

  /**
   * Add a hardware item to the project.
   *
   * @param \Drupal\taxonomy\TermInterface $hardware_item
   *   The hardware items to add to the project.
   *
   * @return $this
   */
  public function addHardwareItem(TermInterface $hardware_item);

}
