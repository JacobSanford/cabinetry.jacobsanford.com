<?php

namespace Drupal\cabinetry_core\Cabinetry;

use Drupal\taxonomy\TermInterface;

/**
 * A generic object to serve as a solid wood part in a cabinetry project.
 */
class HardwareTerm {

  const CABINETRY_HARDWARE_ITEM_PRICE_FIELD = 'field_cabinetry_item_cost';

  /**
   * The hardware Item.
   *
   * @var \Drupal\taxonomy\TermInterface
   */
  private $hardwareItem = NULL;

  /**
   * Constructor.
   *
   * @param \Drupal\taxonomy\TermInterface $hardware_item
   *   The source hardwareItem.
   */
  public function __construct(TermInterface $hardware_item) {
    $this->hardwareItem = $hardware_item;
  }

  /**
   * {@inheritdoc}
   */
  public static function createFromTerm(TermInterface $hardware_item) {
    $obj = new self($hardware_item);
    return $obj;
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->hardwareItem->id();
  }

  /**
   * {@inheritdoc}
   */
  public function getPrice() {
    return (float) $this->hardwareItem->get(self::CABINETRY_HARDWARE_ITEM_PRICE_FIELD)->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->hardwareItem->getName();
  }

  /**
   * {@inheritdoc}
   */
  public function getTerm() {
    return $this->hardwareItem;
  }

}
