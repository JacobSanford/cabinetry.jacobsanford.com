<?php

namespace Drupal\cabinetry_core\Entity;

use Drupal\cabinetry_core\CabinetryPartInterface;
use Drupal\cabinetry_core\Entity\PhysicalObject;
use Drupal\cabinetry_core\StockItemInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the CabinetPart entity.
 *
 * @ingroup cabinetry
 *
 * @ContentEntityType(
 *   id = "cabinetry_part",
 *   label = @Translation("Cabinet Part"),
 *   base_table = "cabinetry_cabinet_part",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */
class CabinetryPart extends PhysicalObject implements CabinetryPartInterface {

  /**
   * Indicates whether this part has been rotated when packing.
   *
   * @var bool
   */
  public $rotated = FALSE;

  /**
   * {@inheritdoc}
   */
  public static function createPart($name, $depth, $width, $height, StockItemInterface $stock_source, $notes, $save = TRUE) {
    $data = [
      'type' => 'cabinetry_part',
      'depth' => (float) $depth,
      'height' => (float) $height,
      'width' => (float) $width,
      'name' => (string) $name,
      'notes' => (string) $notes,
      'stock_source' => $stock_source,
    ];

    $part = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_part')
      ->create($data);

    if ($save != FALSE) {
      $part->save();
    }

    return $part;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // The name to be displayed on the cut sheet and labels for this part.
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of this Cabinetry Part.'))
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 255,
        ]
      );

    // Any specific notes or instructions for this stock item.
    $fields['notes'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Notes'))
      ->setDescription(t('Any notes relating to the Cabinetry Part.'))
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 255,
        ]
      );

    // The cut sheet parts.
    $fields['stock_source'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Stock Source'))
      ->setDescription(t('The stock source this Cabinetry Part should be cut from.'))
      ->setSettings(
        [
          'target_type' => 'cabinetry_stock_item',
          'handler' => 'default',
        ]
      );

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getStockSource() {
    return $this->get('stock_source');
  }

  /**
   * {@inheritdoc}
   */
  public function setStockSource(StockItemInterface $stock_source) {
    $this->set('stock_source', $stock_source);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStockSourceId() {
    return $this->get('stock_source')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setStockSourceId($tid) {
    $this->set('stock_source', $tid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getNotes() {
    return $this->get('notes')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setNotes($notes) {
    $this->set('name', $notes);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRotatedValue() {
    return $this->rotated;
  }

  /**
   * {@inheritdoc}
   */
  public function setRotatedValue($rotated) {
    $this->rotated = $rotated;
    return $this;
  }

}
