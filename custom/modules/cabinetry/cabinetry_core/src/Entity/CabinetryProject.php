<?php

namespace Drupal\cabinetry_core\Entity;

use Drupal\cabinetry_core\Cabinetry\SawBladeTerm;
use Drupal\cabinetry_core\CabinetryPartInterface;
use Drupal\cabinetry_core\CabinetryProjectInterface;
use Drupal\cabinetry_core\CutSheetInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Image\Image;
use Drupal\Core\PhpStorage\PhpStorageFactory;
use Drupal\file\Entity\File;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;

/**
 * Defines the CabinetryProject entity.
 *
 * @ingroup cabinetry
 */
class CabinetryProject extends ContentEntityBase implements CabinetryProjectInterface {

  /**
   * {@inheritdoc}
   *
   * When a new entity instance is added, set the user_id entity reference to
   * the current user as the creator of the instance.
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
  public static function removeCutSheetsBatch($project_eid, array &$context = []) {
    \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->resetCache([$project_eid]);
    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($project_eid);
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    $project->clearCutSheets(TRUE);
    $project->save();

    $context['message'] = t(
      "Removed parts from project #@project_id",
      [
        '@project_id' => $project->id(),
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function removeProjectImagesBatch($project_eid, array &$context = []) {
    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($project_eid);
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    $project->clearProjectImages(TRUE);
    $project->save();

    $context['message'] = t(
      "Removed project images from project #@project_id",
      [
        '@project_id' => $project->id(),
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function removePartsBatch($project_eid, array &$context = []) {
    \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->resetCache([$project_eid]);
    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($project_eid);
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    $project->clearParts(TRUE);
    $project->clearHardwareItems();
    $project->save();

    $context['message'] = t(
      "Removed parts from project #@project_id",
      [
        '@project_id' => $project->id(),
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function removeProjectPhotoBatch($project_eid, array &$context = []) {
    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($project_eid);
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    $project->clearProjectPhoto(TRUE);
    $project->save();

    $context['message'] = t(
      "Removed project photo from project #@project_id",
      [
        '@project_id' => $project->id(),
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Cabinetry Project entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Cabinetry Project entity.'))
      ->setReadOnly(TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The UID of the Cabinetry Project creator.'))
      ->setReadOnly(TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of Cabinetry Project entity.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the Cabinetry Project entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the Cabinetry Project entity was last edited.'));

    // The name of the project.
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Project Name'))
      ->setDescription(t('A name to uniquely identify the Cabinetry Project.'))
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

    // The cut sheets.
    $fields['cut_sheets'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Cut Sheets'))
      ->setDescription(t('The cut sheets attached to the Cabinetry Project.'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSettings(
        [
          'target_type' => 'cabinetry_cut_sheet',
          'handler' => 'default',
        ]
      );

    // The hardware items attached to this project.
    $fields['hardware_items'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Hardware Items'))
      ->setDescription(t('The hardware items attached to the Cabinetry Project.'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setRequired(TRUE)
      ->setSettings(
        [
          'target_type' => 'taxonomy_term',
          'handler' => 'default:taxonomy_term',
          'handler_settings' => [
            'target_bundles' => [
              'cabinetry_hinges' => 'cabinetry_hinges',
              'cabinetry_hinge_plates' => 'cabinetry_hinge_plates',
            ],
          ],
        ]
      );

    // The cut sheet parts.
    $fields['sheet_parts'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Sheet Parts'))
      ->setDescription(t('The cut sheet parts attached to the Cabinetry Project.'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSettings(
        [
          'target_type' => 'cabinetry_part',
          'handler' => 'default',
        ]
      );

    // The saw blade used to cut stock for this project.
    $fields['saw_blade'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Saw Blade'))
      ->setDescription(t('The saw blade that will be used to cut the sheet goods and solid wood pieces.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'target_type' => 'taxonomy_term',
          'handler' => 'default:taxonomy_term',
          'handler_settings' => [
            'target_bundles' => [
              'cabinetry_saw_blades' => 'cabinetry_saw_blades',
            ],
          ],
        ]
      )
      ->setDisplayOptions(
        'view',
        [
          'label' => 'above',
          'type' => 'number',
          'weight' => -10,
        ]
      )
      ->setDisplayOptions(
        'form',
        [
          'type' => 'options_select',
          'weight' => -10,
        ]
      );

    $fields['project_images'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Project Images'))
      ->setDescription(t('Images illustrating the project components.'))
      ->setRequired(TRUE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSettings([
        'file_directory' => 'cabinetry_project_images',
        'alt_field_required' => FALSE,
        'file_extensions' => 'png jpg jpeg',
      ]);

    $fields['project_photo'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Project Illustration'))
      ->setDescription(t('Project Illustration'))
      ->setRequired(TRUE)
      ->setSettings([
        'file_directory' => 'cabinetry_project_photo',
        'alt_field_required' => FALSE,
        'file_extensions' => 'png jpg jpeg',
      ]);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public static function clearProjectCutSheets($eid, $entity_type = NULL, array &$context = []) {
    $project = \Drupal::entityTypeManager()
      ->getStorage($entity_type)
      ->load($eid);
    /* @var $project \Drupal\cabinetry_core\CabinetryProjectInterface */

    // Clear sheet Entities.
    foreach ($project->getCutSheets() as $sheet) {
      /* @var $sheet \Drupal\cabinetry_core\CutSheetInterface */
      $sheet->delete();
    }
    $project->setCutSheets([]);
    $project->save();

    $context['message'] = t(
      "Deleted sheets from @project_name",
      [
        '@project_name' => $project->getName(),
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function clearProjectParts($eid, $entity_type = NULL, array &$context = []) {
    $project = \Drupal::entityTypeManager()
      ->getStorage($entity_type)
      ->load($eid);
    /* @var $project \Drupal\cabinetry_core\CabinetryProjectInterface */

    // Clear attached parts.
    foreach ($project->getParts() as $part) {

      /* @var $part \Drupal\cabinetry_core\CabinetryPartInterface */
      $part->delete();
    }
    $project->setParts([]);
    $project->save();

    $context['message'] = t(
      "Deleted parts from @project_name",
      [
        '@project_name' => $project->getName(),
      ]
    );
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
  public function addCutSheet(CutSheetInterface $cut_sheet) {
    $current_sheets = $this->getCutSheetsIds();
    $current_sheets[] = $cut_sheet->id();
    return $this->setCutSheets($current_sheets);
  }

  /**
   * {@inheritdoc}
   */
  public function getCutSheetsIds() {
    $sheet_ids = [];
    foreach ($this->getCutSheets() as $sheet) {
      $sheet_ids[] = $sheet->id();
    };
    return $sheet_ids;
  }

  /**
   * {@inheritdoc}
   */
  public function getCutSheets() {
    return $this->get('cut_sheets')->referencedEntities();
  }

  /**
   * {@inheritdoc}
   */
  public function setCutSheets(array $cut_sheets) {
    return $this->set('cut_sheets', $cut_sheets);
  }

  /**
   * {@inheritdoc}
   */
  public function setParts(array $sheet_parts) {
    $this->set('sheet_parts', $sheet_parts);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function clearParts($delete_entities = TRUE) {
    if ($delete_entities != FALSE) {
      foreach ($this->getParts() as $part) {
        $part->delete();
      }
    }
    $this->set('cut_sheets', []);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getParts() {
    return $this->get('sheet_parts')->referencedEntities();
  }

  /**
   * {@inheritdoc}
   */
  public function clearCutSheets($delete_entities = TRUE) {
    if ($delete_entities != FALSE) {
      foreach ($this->getCutSheets() as $sheet) {
        $sheet->delete();
      }
    }
    $this->set('sheet_parts', []);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function clearProjectImages($delete_entities = TRUE) {
    if ($delete_entities != FALSE) {
      foreach ($this->getProjectImages() as $image) {
        $image->delete();
      }
    }
    $this->set('project_images', []);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getProjectImages() {
    return $this->get('project_images')->referencedEntities();
  }

  /**
   * {@inheritdoc}
   */
  public function getPartIds() {
    $part_ids = [];
    foreach ($this->getParts() as $part) {
      $part_ids[] = $part->id();
    };
    return $part_ids;
  }

  /**
   * {@inheritdoc}
   */
  public function addPart(CabinetryPartInterface $sheet_part) {
    $this->get('sheet_parts')->appendItem($sheet_part->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSawBladeCutWidth() {
    $saw_blade = SawBladeTerm::createFromTerm($this->getSawBlade());
    return $saw_blade->getCutWidth();
  }

  /**
   * {@inheritdoc}
   */
  public function getSawBlade() {
    return $this->get('saw_blade')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setSawBlade(TermInterface $saw_blade) {
    return $this->set('sheet_parts', $saw_blade);
  }

  /**
   * {@inheritdoc}
   */
  public function setHardwareItems(array $hardware_items) {
    $this->set('hardware_items', $hardware_items);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function clearHardwareItems() {
    $this->set('hardware_items', []);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getHardwareItemIds() {
    $hardware_ids = [];
    foreach ($this->getHardwareItems() as $item) {
      $hardware_ids[] = $item->id();
    };
    return $hardware_ids;
  }

  /**
   * {@inheritdoc}
   */
  public function getHardwareItems() {
    return $this->get('hardware_items')->referencedEntities();
  }

  /**
   * {@inheritdoc}
   */
  public function addHardwareItem(TermInterface $hardware_item) {
    $this->get('hardware_items')->appendItem($hardware_item->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addProjectImage(File $project_image) {
    $this->get('project_images')->appendItem($project_image->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function batchClearComponents() {
    $batch = [
      'title' => t('Removing Entities Relating To Project'),
      'init_message' => t('Removing cabinet project entities'),
      'operations' => [],
    ];

    // Delete current cut_sheet entities.
    $batch['operations'][] = [
      [
        'Drupal\cabinetry_core\Entity\CabinetryProject',
        'removeCutSheetsBatch',
      ],
      [$this->id()],
    ];

    // Delete sheet parts.
    $batch['operations'][] = [
      [
        'Drupal\cabinetry_core\Entity\CabinetryProject',
        'removePartsBatch',
      ],
      [$this->id()],
    ];

    // Delete project images.
    $batch['operations'][] = [
      [
        'Drupal\cabinetry_core\Entity\CabinetryProject',
        'removeProjectImagesBatch',
      ],
      [$this->id()],
    ];

    // Delete project images.
    $batch['operations'][] = [
      [
        'Drupal\cabinetry_core\Entity\CabinetryProject',
        'removeProjectPhotoBatch',
      ],
      [$this->id()],
    ];

    // Reset twig cache.
    PhpStorageFactory::get('twig')->deleteAll();

    batch_set($batch);
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
  public function getProjectPhoto() {
    return $this->get('project_photo');
  }

  /**
   * {@inheritdoc}
   */
  public function setProjectPhoto($project_photo) {
    $this->set('project_photo', $project_photo);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function clearProjectPhoto($delete_entities = TRUE) {
    if ($delete_entities != FALSE) {
      $this->getProjectPhoto()->delete();
    }
    $this->set('project_photo', NULL);
    return $this;
  }

}
