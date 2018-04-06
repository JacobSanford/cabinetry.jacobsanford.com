<?php

namespace Drupal\cabinetry_wainscoting_project\Entity;

use Drupal\cabinetry_wainscoting_project\WainscotingWallInterface;
use Drupal\cabinetry_core\Entity\PhysicalObject;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Defines the CabinetModule entity.
 *
 * @ingroup cabinetry
 *
 * @ContentEntityType(
 *   id = "cabinetry_wainscot_wall",
 *   label = @Translation("Cabinet Wainscoting Wall"),
 *   base_table = "cabinetry_wainscot_wall",
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\cabinetry_wainscoting_project\Form\WainscotingWallForm",
 *       "edit" = "Drupal\cabinetry_wainscoting_project\Form\WainscotingWallForm",
 *       "delete" = "Drupal\cabinetry_wainscoting_project\Form\WainscotingWallDeleteForm",
 *     },
 *     "list_builder" = "Drupal\cabinetry_wainscoting_project\Entity\Controller\WainscotingWallListBuilder",
 *     "views_data" = "Drupal\cabinetry_wainscoting_project\Entity\WainscotingWallViewsData",
 *     "access" = "Drupal\cabinetry_wainscoting_project\WainscotingWallAccessControlHandler",
 *   },
 *   links = {
 *     "collection" = "/cabinetry/wainscoting_wall/list",
 *     "edit-form" = "/cabinetry/wainscoting_wall/{cabinetry_wainscot_wall}/edit",
 *     "delete-form" = "/cabinetry/wainscoting_wall/{cabinetry_wainscot_wall}/delete",
 *     "canonical" = "/cabinetry/wainscoting_wall/{cabinetry_wainscot_wall}",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */
class WainscotingWall extends PhysicalObject implements WainscotingWallInterface {

  /**
   * The module carcass, type CabinetCarcass.
   *
   * @var object
   */
  public $carcass = NULL;

  /**
   * The hardware for the module, type CabinetHardware.
   *
   * @var object
   */
  public $hardware = [];

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // The name of the module.
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Module Name'))
      ->setDescription(t('A name to uniquely identify this Wainscoting Wall.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 255,
          'text_processing' => 0,
        ]
      )
      ->setDisplayOptions(
        'form',
        [
          'type' => 'string_textfield',
          'weight' => -10,
        ]
      );

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function delete() {
    // Remove module from parent item.
    $parent_project = $this->getParentProject();
    $parent_project->removeWall($this);
    $parent_project->save();

    parent::delete();
  }

  /**
   * {@inheritdoc}
   */
  public function getParentProject() {
    $query = \Drupal::service('entity.query')
      ->get('cabinetry_wainscoting_project')
      ->condition('wainscot_walls', $this->id());
    $entity_ids = $query->execute();

    foreach ($entity_ids as $entity_id) {
      return \Drupal::entityTypeManager()
        ->getStorage('cabinetry_wainscoting_project')
        ->load($entity_id);
    }

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

}
