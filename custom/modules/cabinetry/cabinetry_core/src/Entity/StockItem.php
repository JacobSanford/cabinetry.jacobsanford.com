<?php

namespace Drupal\cabinetry_core\Entity;

use Drupal\cabinetry_core\StockItemInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\taxonomy\TermInterface;

/**
 * Defines the CutSheet entity.
 *
 * @ingroup cabinetry
 *
 * @ContentEntityType(
 *   id = "cabinetry_stock_item",
 *   label = @Translation("Cabinetry Stock Item"),
 *   base_table = "cabinetry_stock_item",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */
class StockItem extends PhysicalObject implements StockItemInterface {

  /**
   * {@inheritdoc}
   */
  public static function createLoadItem($name, $depth, $width, $height, TermInterface $material, $preserve_grain) {
    // Check if this item exists.
    $query = \Drupal::service('entity.query')
      ->get('cabinetry_stock_item')
      ->condition('depth', $depth)
      ->condition('width', $width)
      ->condition('height', $height)
      ->condition('material', $material->id());

    $entity_ids = $query->execute();

    foreach ($entity_ids as $entity_id) {
      return \Drupal::entityTypeManager()
        ->getStorage('cabinetry_stock_item')
        ->load($entity_id);
    }

    // Create item, not found.
    $data = [
      'type' => 'cabinetry_stock_item',
      'depth' => (float) $depth,
      'height' => (float) $height,
      'width' => (float) $width,
      'name' => (string) $name,
      'material' => $material,
      'preserve_grain' => $preserve_grain,
    ];

    $stock_item = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_stock_item')
      ->create($data);
    $stock_item->save();
    return $stock_item;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // The name of this stock source.
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Stock Source Material'))
      ->setDescription(t('A textual description of the material used for this Stock Source .'))
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 255,
        ]
      );

    // The material of this sheet.
    $fields['material'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Material'))
      ->setDescription(t('The taxonomy material term the Stock Source is made of.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'target_type' => 'taxonomy_term',
          'handler' => 'default:taxonomy_term',
          'handler_settings' => [
            'target_bundles' => [
              'cabinetry_sheet_goods' => 'cabinetry_sheet_goods',
              'cabinetry_solid_stock' => 'cabinetry_solid_stock',
            ],
          ],
        ]
      );

    // Should the directionality of the grain be preserved?
    $fields['preserve_grain'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Preserve Grain'))
      ->setDescription(t('Should the directionality of the Stock Source grain be preserved?'));

    return $fields;
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
  public function getMaterial() {
    return $this->get('material')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setMaterial(TermInterface $material) {
    $this->set('material', $material->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMaterialId() {
    return $this->get('material')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setMaterialId($tid) {
    $this->set('material', $tid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPreserveGrain() {
    return (bool) $this->get('preserve_grain');
  }

}
