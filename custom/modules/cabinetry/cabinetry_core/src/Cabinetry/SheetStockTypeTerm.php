<?php

namespace Drupal\cabinetry_core\Cabinetry;

use Drupal\taxonomy\TermInterface;

/**
 * A generic object to serve as a solid wood part in a cabinetry project.
 */
class SheetStockTypeTerm {

  const CABINETRY_MATERIAL_DEPTH_FIELD = 'field_cabinetry_depth';
  const CABINETRY_MATERIAL_HEIGHT_FIELD = 'field_cabinetry_height';
  const CABINETRY_MATERIAL_WIDTH_FIELD = 'field_cabinetry_width';
  const CABINETRY_MATERIAL_PRESERVE_GRAIN_FIELD = 'field_cabinetry_preserve_grain';
  const CABINETRY_MATERIAL_SHEET_COST = 'field_cabinetry_item_cost';

  /**
   * The material type of the part.
   *
   * @var \Drupal\taxonomy\TermInterface
   */
  private $material = NULL;

  /**
   * Constructor.
   *
   * @param \Drupal\taxonomy\TermInterface $material
   *   The source material this sheet good is made out of.
   */
  public function __construct(TermInterface $material) {
    $this->material = $material;
  }

  /**
   * {@inheritdoc}
   */
  public static function createFromTerm(TermInterface $material) {
    $obj = new self($material);
    return $obj;
  }

  /**
   * {@inheritdoc}
   */
  public function getCost() {
    return $this->material->get(self::CABINETRY_MATERIAL_SHEET_COST)->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function getDepth() {
    return $this->material->get(self::CABINETRY_MATERIAL_DEPTH_FIELD)->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function getHeight() {
    return $this->material->get(self::CABINETRY_MATERIAL_HEIGHT_FIELD)->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->material->getName();
  }

  /**
   * {@inheritdoc}
   */
  public function getWidth() {
    return $this->material->get(self::CABINETRY_MATERIAL_WIDTH_FIELD)->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function getEntity() {
    return $this->material;
  }

  /**
   * {@inheritdoc}
   */
  public function getVocabularyId() {
    return $this->material->getVocabularyId();
  }

  /**
   * {@inheritdoc}
   */
  public function getPreserveGrain() {
    return (bool) $this->material->get(self::CABINETRY_MATERIAL_PRESERVE_GRAIN_FIELD)->getString();
  }

}
