<?php

namespace Drupal\cabinetry_core\Cabinetry;

use Drupal\taxonomy\TermInterface;

/**
 * A generic object to interface with a solid stock taxonomy term.
 */
class SolidStockTypeTerm {

  const CABINETRY_MATERIAL_WIDTH_FIELD = 'field_cabinetry_width';
  const CABINETRY_MATERIAL_BOARDFOOT_FIELD = 'field_cabinetry_boardfood_cost';
  const CABINETRY_MATERIAL_BOARDFOOT_TO_MM3 = 2.36e+6;

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
   *   The source material this solid stock is made out of.
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
  public function getBoardfootCost() {
    return $this->material->get(self::CABINETRY_MATERIAL_BOARDFOOT_FIELD)->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function getMm3Cost() {
    return $this->material->get(self::CABINETRY_MATERIAL_BOARDFOOT_FIELD)->getString() / self::CABINETRY_MATERIAL_BOARDFOOT_TO_MM3;
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
    return TRUE;
  }

}
