<?php

namespace Drupal\cabinetry_core\Entity;

use Drupal\cabinetry_core\CutSheetInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Image\Image;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;

/**
 * Defines the CutSheet entity.
 *
 * @ingroup cabinetry
 *
 * @ContentEntityType(
 *   id = "cabinetry_cut_sheet",
 *   label = @Translation("Material Cut Sheet"),
 *   base_table = "cabinetry_cut_sheet",
 *   handlers = {
 *     "views_data" = "Drupal\cabinetry_core\Entity\CutSheetViewsData",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */
class CutSheet extends PhysicalObject implements CutSheetInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // The name of this sheet, typically the material and dimensions.
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Material Name'))
      ->setDescription(t('A textual description of this cut sheet material.'))
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 255,
        ]
      );

    // The material of this sheet.
    $fields['material'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Material'))
      ->setDescription(t('The taxonomy material term the cut sheet is made of.'))
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

    // The images generated displaying the cut layout of the cabinets.
    $fields['cut_sheet_images'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Cut Sheet Images'))
      ->setDescription(t('Images identifying the cuts required on the sheet.'))
      ->setRequired(TRUE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSettings([
        'file_directory' => 'cut_sheet_images',
        'alt_field_required' => FALSE,
        'file_extensions' => 'png jpg jpeg',
      ])
      ->setDisplayOptions(
        'view',
        [
          'label' => 'hidden',
          'type' => 'default',
          'weight' => 0,
        ]
      )
      ->setDisplayOptions(
        'form',
        [
          'label' => 'hidden',
          'type' => 'image_image',
          'weight' => 0,
        ]
      )
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function delete() {
    // Remove files from filesystem.
    foreach ($this->getCutSheetImages() as $file_object) {
      $file_object->delete();
    }

    parent::delete();
  }

  /**
   * {@inheritdoc}
   */
  public function getCutSheetImages() {
    return $this->get('cut_sheet_images')->referencedEntities();
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
  public function setCutSheetImages(array $sheet_images) {
    $sheet_image_ids = [];
    foreach ($sheet_images as $sheet_image) {
      $sheet_ids[] = $sheet_image->id();
    }
    $this->set('cut_sheet_images', $sheet_image_ids);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCutSheetImageIds() {
    $cut_sheet_images = [];
    foreach ($this->get('cut_sheet_images') as $cut_sheet_image) {
      $cut_sheet_images[] = $cut_sheet_image->target_id;
    }
    return $cut_sheet_images;
  }

  /**
   * {@inheritdoc}
   */
  public function addCutSheetImage(Image $sheet_image) {
    $this->get('cut_sheet_images')->appendItem($sheet_image);
    return $this;
  }

}
