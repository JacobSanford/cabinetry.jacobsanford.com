<?php

namespace Drupal\cabinetry_core\Entity;

use Drupal\cabinetry_core\PhysicalObjectInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;

/**
 * Defines the PhysicalObject entity.
 *
 * @ingroup cabinetry
 */
class PhysicalObject extends ContentEntityBase implements PhysicalObjectInterface {

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Physical Object entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Physical Object entity.'))
      ->setReadOnly(TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The UID of the creator.'))
      ->setReadOnly(TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of Physical Object entity.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    // The outer width (x) of the cabinet item, in millimeters.
    $fields['width'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Width'))
      ->setDescription(t('The outer Width of the cabinet module.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'default_value' => '',
          'precision' => 10,
          'scale' => 2,
          'decimal_separator' => '.',
          'min' => 0,
          'suffix' => t('mm'),
        ]
      )
      ->setDisplayOptions(
        'view',
        [
          'label' => 'above',
          'type' => 'number',
          'weight' => -5,
        ]
      )
      ->setDisplayOptions(
        'form',
        [
          'type' => 'number',
          'weight' => -5,
        ]
      )
      ->setDefaultValue(480.0)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // The outer height (y) of the cabinet item, in millimeters.
    $fields['height'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Height'))
      ->setDescription(t('The outer Height of the cabinet module.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'default_value' => '',
          'precision' => 10,
          'scale' => 2,
          'decimal_separator' => '.',
          'min' => 0,
          'suffix' => t('mm'),
        ]
      )
      ->setDisplayOptions(
        'view',
        [
          'label' => 'above',
          'type' => 'number',
          'weight' => -5,
        ]
      )
      ->setDisplayOptions(
        'form',
        [
          'type' => 'number',
          'weight' => -5,
        ]
      )
      ->setDefaultValue(768.0)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // The outer depth (z) of the cabinet item, in millimeters.
    $fields['depth'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Depth'))
      ->setDescription(t('The outer Depth of the cabinet module.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'default_value' => '',
          'precision' => 10,
          'scale' => 2,
          'decimal_separator' => '.',
          'min' => 0,
          'suffix' => t('mm'),
        ]
      )
      ->setDisplayOptions(
        'view',
        [
          'label' => 'above',
          'type' => 'number',
          'weight' => -5,
        ]
      )
      ->setDisplayOptions(
        'form',
        [
          'type' => 'number',
          'weight' => -5,
        ]
      )
      ->setDefaultValue(576.0)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setChangedTime($timestamp) {
    $this->set('changed', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTimeAcrossTranslations() {
    $changed = $this->getUntranslated()->getChangedTime();
    foreach ($this->getTranslationLanguages(FALSE) as $language) {
      $translation_changed = $this->getTranslation($language->getId())
        ->getChangedTime();
      $changed = max($translation_changed, $changed);
    }
    return $changed;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDepth() {
    return $this->get('depth')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setDepth($depth) {
    $this->set('depth', $depth);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getHeight() {
    return $this->get('height')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setHeight($height) {
    $this->set('height', $height);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getWidth() {
    return $this->get('width')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setWidth($width) {
    $this->set('width', $width);
    return $this;
  }

}
