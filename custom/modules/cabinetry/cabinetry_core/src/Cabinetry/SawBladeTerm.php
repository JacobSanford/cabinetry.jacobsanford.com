<?php

namespace Drupal\cabinetry_core\Cabinetry;

use Drupal\taxonomy\TermInterface;

/**
 * An object to serve as an interface to a saw blade taxonomy term.
 */
class SawBladeTerm {

  const CABINETRY_SAW_BLADE_CUT_WIDTH_FIELD = 'field_cabinetry_width';
  /**
   * The saw blade.
   *
   * @var \Drupal\taxonomy\TermInterface
   */
  private $blade = NULL;

  /**
   * Constructor.
   *
   * @param \Drupal\taxonomy\TermInterface $blade
   *   The source bit.
   */
  public function __construct(TermInterface $blade) {
    $this->blade = $blade;
  }

  /**
   * {@inheritdoc}
   */
  public static function createFromTerm(TermInterface $blade) {
    $obj = new self($blade);
    return $obj;
  }

  /**
   * {@inheritdoc}
   */
  public function getCutWidth() {
    return (float) $this->blade->get(self::CABINETRY_SAW_BLADE_CUT_WIDTH_FIELD)->getString();
  }

}
