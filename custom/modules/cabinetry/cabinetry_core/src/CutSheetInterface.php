<?php

namespace Drupal\cabinetry_core;

use Drupal\cabinetry_core\PhysicalObjectInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Image\Image;
use Drupal\taxonomy\TermInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a Cut Sheet entity.
 *
 * @ingroup cabinetry
 */
interface CutSheetInterface extends PhysicalObjectInterface {

  /**
   * Gets the cut sheet images for this cut sheet.
   *
   * @return \Drupal\Core\Image\Image[]
   *   An array of image entities.
   */
  public function getCutSheetImages();

  /**
   * Sets the cut sheet image files.
   *
   * @param \Drupal\Core\Image\Image[] $sheet_images
   *   An array of image objects to set.
   *
   * @return $this
   */
  public function setCutSheetImages(array $sheet_images);

  /**
   * Gets the file ID values of the cut sheet images.
   *
   * @return int[]
   *   The file IDs of the cut sheets image attached to the cut sheet.
   */
  public function getCutSheetImageIds();

  /**
   * Add an image to the cut sheet object.
   *
   * @param \Drupal\Core\Image\Image $sheet_image
   *   An image object to add to the cut sheet object.
   *
   * @return $this
   */
  public function addCutSheetImage(Image $sheet_image);

  /**
   * Gets the material object of the cut sheet.
   *
   * @return \Drupal\taxonomy\TermInterface
   *   A term representing the material of the sheet.
   */
  public function getMaterial();

  /**
   * Sets the material of the cut sheet.
   *
   * @param \Drupal\taxonomy\TermInterface $material
   *   The material term.
   *
   * @return $this
   */
  public function setMaterial(TermInterface $material);

  /**
   * Gets the material tid of the cut sheet.
   *
   * @return int
   *   The term tid of the material.
   */
  public function getMaterialId();

  /**
   * Sets the material tid of the cut sheet.
   *
   * @param int $tid
   *   The tid of the material term.
   *
   * @return $this
   */
  public function setMaterialId($tid);

  /**
   * Gets the name of the cut sheet.
   *
   * @return string
   *   The name of the object.
   */
  public function getName();

  /**
   * Sets the name of the cut sheet.
   *
   * @param string $name
   *   The name of the object.
   *
   * @return $this
   */
  public function setName($name);

}
