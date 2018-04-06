<?php

namespace Drupal\cabinetry_core\Cabinetry;

use Drupal\taxonomy\TermInterface;

/**
 * A generic object to serve as edge banding type in a cabinetry project.
 */
class EdgeBanding {

  /**
   * The length of banding, in millimeters.
   *
   * @var float
   */
  public $length = 0.0;

  /**
   * The material of edging to generate.
   *
   * @var \Drupal\taxonomy\TermInterface
   */
  public $material = NULL;

  /**
   * Constructor.
   *
   * @param \Drupal\taxonomy\TermInterface $material
   *   The edge banding material.
   */
  public function __construct(TermInterface $material) {
    $this->material = $material;
  }

  /**
   * Add additional length to this object.
   *
   * @param float $length
   *   The amount to add, in millimeters.
   */
  public function add($length) {
    $this->length += $length;
  }

}
