<?php

namespace Drupal\cabinetry_core\Cabinetry;

use Drupal\taxonomy\TermInterface;

/**
 * A generic object to serve as a solid wood part in a cabinetry project.
 */
class RouterBitTerm {

  const CABINETRY_RAILSTILE_BIT_CUT_DEPTH_FIELD = 'field_cabinetry_rail_cut_depth';

  /**
   * The bit type of the part.
   *
   * @var \Drupal\taxonomy\TermInterface
   */
  private $bit = NULL;

  /**
   * Constructor.
   *
   * @param \Drupal\taxonomy\TermInterface $bit
   *   The source bit.
   */
  public function __construct(TermInterface $bit) {
    $this->bit = $bit;
  }

  /**
   * {@inheritdoc}
   */
  public static function createFromTerm(TermInterface $bit) {
    $obj = new self($bit);
    return $obj;
  }

  /**
   * {@inheritdoc}
   */
  public function getCutDepth() {
    return $this->bit->get(self::CABINETRY_RAILSTILE_BIT_CUT_DEPTH_FIELD)->getString();
  }

}
