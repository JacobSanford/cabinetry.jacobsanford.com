<?php

namespace Drupal\cabinetry_cabinet_project\Entity;

use Drupal\cabinetry_cabinet_project\CabinetModuleInterface;
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
 *   id = "cabinetry_cabinet_module",
 *   label = @Translation("Cabinet Module"),
 *   base_table = "cabinetry_cabinet_module",
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\cabinetry_cabinet_project\Form\CabinetModuleForm",
 *       "edit" = "Drupal\cabinetry_cabinet_project\Form\CabinetModuleForm",
 *       "delete" = "Drupal\cabinetry_cabinet_project\Form\CabinetModuleDeleteForm",
 *     },
 *     "list_builder" = "Drupal\cabinetry_cabinet_project\Entity\Controller\CabinetModuleListBuilder",
 *     "views_data" = "Drupal\cabinetry_cabinet_project\Entity\CabinetModuleViewsData",
 *     "access" = "Drupal\cabinetry_cabinet_project\CabinetProjectAccessControlHandler",
 *   },
 *   links = {
 *     "collection" = "/cabinetry/cabinet_module/list",
 *     "edit-form" = "/cabinetry/cabinet_module/{cabinetry_cabinet_module}/edit",
 *     "delete-form" = "/cabinetry/cabinet_module/{cabinetry_cabinet_module}/delete",
 *     "canonical" = "/cabinetry/cabinet_module/{cabinetry_cabinet_module}",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */
class CabinetModule extends PhysicalObject implements CabinetModuleInterface {

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
      ->setDescription(t('A name to uniquely identify this Cabinet Module.'))
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

    // The label of the class used to build this module.
    $fields['class_label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Class Label'))
      ->setDescription(t('The label of the class used to build this Cabinet Module.'))
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 255,
          'text_processing' => 0,
        ]
      );

    // The name of the class used to build this module.
    $fields['class'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Class'))
      ->setDescription(t('The class used to build this Cabinet Module.'))
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 255,
        ]
      );

    // The number of shelves to generate for the module.
    $fields['shelves'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Shelves'))
      ->setDescription(t('The number of shelves in the Cabinet Module.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'default_value' => 0,
          'min' => 0,
          'max' => 5,
          'step' => 1,
        ]
      )
      ->setDisplayOptions(
        'form',
        [
          'type' => 'number',
          'weight' => -3,
        ]
      )
      ->setDefaultValue(0)
      ->setDisplayConfigurable('form', TRUE);

    // Where doors exist, the number of doors across the carcass opening.
    $fields['doors_across_gap'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Doors Across Gap'))
      ->setDescription(t('The number of doors across the front opening of the Cabinet Module. Set to 0 for no doors.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'default_value' => 1,
          'min' => 0,
          'max' => 2,
          'step' => 1,
        ]
      )
      ->setDisplayOptions(
        'form',
        [
          'type' => 'number',
          'weight' => -3,
        ]
      )
      ->setDefaultValue(1)
      ->setDisplayConfigurable('form', TRUE);

    // The ratio of divisions in the cabinet.
    $fields['divisions'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Divisions'))
      ->setDescription(t('The ratio of vertical divisions in the Cabinet Module.'))
      ->setRequired(TRUE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSettings(
        [
          'default_value' => '',
          'precision' => 6,
          'scale' => 5,
          'decimal_separator' => '.',
          'min' => 0.0,
          'max' => 1.0,
        ]
      )
      ->setDisplayOptions(
        'view',
        [
          'label' => 'above',
          'type' => 'number',
          'weight' => -1,
        ]
      )
      ->setDisplayOptions(
        'form',
        [
          'type' => 'number',
          'weight' => -1,
        ]
      )
      ->setDefaultValue([1.0])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Is a counter on top of this module?
    $fields['counter'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Counter on Top'))
      ->setDescription(t('Whether or not a counter is on top of the Cabinet Module.'))
      ->setDisplayOptions(
        'form',
        [
          'type' => 'boolean_checkbox',
          'weight' => 0,
          'settings' => [
            'default_value' => 0,
            'display_label' => TRUE,
          ],
        ]
      )
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function delete() {
    // Remove module from parent item.
    $parent_project = $this->getParentProject();
    $parent_project->removeCabinetModule($this);
    $parent_project->save();

    parent::delete();
  }

  /**
   * {@inheritdoc}
   */
  public function getParentProject() {
    $query = \Drupal::service('entity.query')
      ->get('cabinetry_cabinet_project')
      ->condition('cabinet_modules', $this->id());
    $entity_ids = $query->execute();

    foreach ($entity_ids as $entity_id) {
      return \Drupal::entityTypeManager()
        ->getStorage('cabinetry_cabinet_project')
        ->load($entity_id);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getClass() {
    return $this->get('class')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setClass($class) {
    $this->set('class', $class);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getClassLabel() {
    return $this->get('class_label')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setClassLabel($label) {
    $this->set('class_label', $label);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCounterOnTop() {
    return $this->get('counter')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCounterOnTop($counter_on_top) {
    $this->set('counter', $counter_on_top);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDivisionRatios() {
    $divisions = [];
    foreach ($this->get('divisions') as $division) {
      $divisions[] = $division->value;
    }
    return $divisions;
  }

  /**
   * {@inheritdoc}
   */
  public function setDivisionRatios(array $divisions) {
    $this->set('divisions', $divisions);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDoorsAcrossGap() {
    return $this->get('doors_across_gap')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setDoorsAcrossGap($doors_across_gap) {
    $this->set('doors_across_gap', $doors_across_gap);
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
  public function getNumShelves() {
    return $this->get('shelves')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setNumShelves($num_shelves) {
    $this->set('shelves', $num_shelves);
    return $this;
  }

}
