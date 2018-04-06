<?php

namespace Drupal\cabinetry_wainscoting_project\Entity;

use Drupal\cabinetry_wainscoting_project\WainscotingProjectInterface;
use Drupal\cabinetry_core\Entity\CabinetryProject;


use Drupal\cabinetry_core\Cabinetry\SheetStockTypeTerm;
use Drupal\cabinetry_core\Cabinetry\SolidStockTypeTerm;
use Drupal\cabinetry_core\ImageWriter;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\File\FileSystem;
use Drupal\Core\PhpStorage\PhpStorageFactory;
use Drupal\file\Entity\File;
use Drupal\taxonomy\TermInterface;

/**
 * Defines the Wainscoting Project entity.
 *
 * @ingroup cabinetry
 *
 * @ContentEntityType(
 *   id = "cabinetry_wainscoting_project",
 *   label = @Translation("Wainscoting Project"),
 *   base_table = "cabinetry_wainscoting_project",
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\cabinetry_wainscoting_project\Form\WainscotingProjectForm",
 *       "edit" = "Drupal\cabinetry_wainscoting_project\Form\WainscotingProjectForm",
 *       "delete" = "Drupal\cabinetry_wainscoting_project\Form\WainscotingProjectDeleteForm",
 *     },
 *     "views_data" = "Drupal\cabinetry_wainscoting_project\Entity\WainscotingProjectViewsData",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\cabinetry_wainscoting_project\Entity\Controller\WainscotingProjectListBuilder",
 *     "access" = "Drupal\cabinetry_wainscoting_project\WainscotingProjectAccessControlHandler",
 *   },
 *   links = {
 *     "collection" = "/cabinetry/wainscoting_project/list",
 *     "edit-form" = "/cabinetry/wainscoting_project/{cabinetry_wainscoting_project}/edit",
 *     "delete-form" = "/cabinetry/wainscoting_project/{cabinetry_wainscoting_project}/delete",
 *     "canonical" = "/cabinetry/wainscoting_project/{cabinetry_wainscoting_project}",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */
class WainscotingProject extends CabinetryProject implements WainscotingProjectInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // The height of the wainscot on the wall.
    $fields['wainscot_wall_height'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Wainscot Height on Wall'))
      ->setDescription(t('The height of the wainscot from the floor, in mm.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'min' => 0,
          'precision' => 10,
          'scale' => 2,
          'decimal_separator' => '.',
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
      ->setDefaultValue(812.8)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Wainscot frame material.
    $fields['wainscot_frame'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Wainscot Frame Material'))
      ->setDescription(t('The wainscot frame material.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'target_type' => 'taxonomy_term',
          'handler' => 'default:taxonomy_term',
          'handler_settings' => [
            'target_bundles' => [
              'cabinetry_solid_stock' => 'cabinetry_solid_stock',
            ],
          ],
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
          'type' => 'options_select',
          'weight' => -1,
        ]
      );

    // The height of the wainscot bottom rail.
    $fields['wainscot_bottom_rail_height'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Wainscot Bottom Rail Height'))
      ->setDescription(t('The height of the wainscot bottom rail, in mm.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'min' => 0,
          'precision' => 10,
          'scale' => 2,
          'decimal_separator' => '.',
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
      ->setDefaultValue(114.3)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // The height of the wainscot top rail.
    $fields['wainscot_top_rail_height'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Wainscot Top Rail Height'))
      ->setDescription(t('The height of the wainscot top rail, in mm.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'min' => 0,
          'precision' => 10,
          'scale' => 2,
          'decimal_separator' => '.',
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
      ->setDefaultValue(88.9)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // The height of the wainscot stiles.
    $fields['wainscot_stile_height'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Wainscot Stile Height'))
      ->setDescription(t('The height of the wainscot stiles, in mm.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'min' => 0,
          'precision' => 10,
          'scale' => 2,
          'decimal_separator' => '.',
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
      ->setDefaultValue(88.9)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // The height of the wainscot cap.
    $fields['wainscot_cap_board_height'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Wainscot Cap Board Height'))
      ->setDescription(t('The height of the wainscot cap, in mm.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'min' => 0,
          'precision' => 10,
          'scale' => 2,
          'decimal_separator' => '.',
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
      ->setDefaultValue(88.9)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Wainscot panel material.
    $fields['wainscot_panel'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Wainstcot Panel Material'))
      ->setDescription(t('The material to be used for the wainscot panel.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'target_type' => 'taxonomy_term',
          'handler' => 'default:taxonomy_term',
          'handler_settings' => [
            'target_bundles' => [
              'cabinetry_sheet_goods' => 'cabinetry_sheet_goods',
            ],
          ],
        ]
      )
      ->setDisplayOptions(
        'view',
        [
          'label' => 'above',
          'type' => 'number',
          'weight' => -2,
        ]
      )
      ->setDisplayOptions(
        'form',
        [
          'type' => 'options_select',
          'weight' => -2,
        ]
      );

    // Baseboard panel material.
    $fields['baseboard_panel'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Baseboard Panel Material'))
      ->setDescription(t('The material to be used for the baseboard panel.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'target_type' => 'taxonomy_term',
          'handler' => 'default:taxonomy_term',
          'handler_settings' => [
            'target_bundles' => [
              'cabinetry_sheet_goods' => 'cabinetry_sheet_goods',
            ],
          ],
        ]
      )
      ->setDisplayOptions(
        'view',
        [
          'label' => 'above',
          'type' => 'number',
          'weight' => -2,
        ]
      )
      ->setDisplayOptions(
        'form',
        [
          'type' => 'options_select',
          'weight' => -2,
        ]
      );

    // The height of the baseboard panel.
    $fields['baseboard_height'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Baseboard Height'))
      ->setDescription(t('The height of the baseboard, in mm.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'min' => 0,
          'precision' => 10,
          'scale' => 2,
          'decimal_separator' => '.',
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
      ->setDefaultValue(127.0)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Trim material.
    $fields['trim_material'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Trim Material'))
      ->setDescription(t('The trim material.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'target_type' => 'taxonomy_term',
          'handler' => 'default:taxonomy_term',
          'handler_settings' => [
            'target_bundles' => [
              'cabinetry_solid_stock' => 'cabinetry_solid_stock',
            ],
          ],
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
          'type' => 'options_select',
          'weight' => -1,
        ]
      );

    $fields['wainscot_walls'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Wainscot Walls'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSettings(
        [
          'target_type' => 'cabinetry_wainscot_wall',
          'handler' => 'default',
        ]
      );

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getDoorPanelUndersize() {
    return (float) $this->get('door_panel_undersize')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setDoorPanelUndersize($undersize) {
    $this->set('door_panel_undersize', $undersize);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDoorReveal() {
    return (float) $this->get('door_reveal')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setDoorReveal($reveal) {
    $this->set('door_reveal', $reveal);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDoorFrameMaterial() {
    return SolidStockTypeTerm::createFromTerm(
      $this->get('door_frame')->entity
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setDoorFrameMaterial(TermInterface $material) {
    $this->set('door_frame', $material->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDoorFrameMaterialId() {
    return (int) $this->get('door_frame')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setDoorFrameMaterialId($tid) {
    $this->set('door_frame', $tid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCarcassBackMaterial() {
    return SheetStockTypeTerm::createFromTerm(
      $this->get('carcass_back')->entity
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setCarcassBackMaterial(TermInterface $material) {
    $this->set('carcass_back', $material->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCarcassBackMaterialId() {
    return (int) $this->get('carcass_back')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setCarcassBackMaterialId($tid) {
    $this->set('carcass_back', $tid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCarcassMaterial() {
    return SheetStockTypeTerm::createFromTerm(
      $this->get('carcass_sides')->entity
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setCarcassMaterial(TermInterface $material) {
    $this->set('carcass_sides', $material->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCarcassMaterialId() {
    return (int) $this->get('carcass_sides')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setCarcassMaterialId($tid) {
    $this->set('carcass_sides', $tid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDoorPanelMaterial() {
    return SheetStockTypeTerm::createFromTerm(
      $this->get('door_panel')->entity
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setDoorPanelMaterial(TermInterface $material) {
    $this->set('door_panel', $material->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDoorPanelMaterialId() {
    return (int) $this->get('door_panel')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setDoorPanelMaterialId($tid) {
    $this->set('door_panel', $tid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDoorRouterBit() {
    return $this->get('door_router_bit')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setDoorRouterBit(TermInterface $material) {
    $this->set('door_router_bit', $material->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDoorRouterBitId() {
    return (int) $this->get('door_router_bit')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setDoorRouterBitId($tid) {
    $this->set('door_router_bit', $tid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIsThirtyTwoSystem() {
    return (bool) $this->get('counter_top')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setIsThirtyTwoSystem($is_thirty_two) {
    $this->set('counter_top', $is_thirty_two);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPrimaryHinge() {
    return $this->get('primary_hinge')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setPrimaryHinge(TermInterface $material) {
    $this->set('primary_hinge', $material->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPrimaryHingeId() {
    return (int) $this->get('primary_hinge')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setPrimaryHingeId($tid) {
    $this->set('primary_hinge', $tid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPrimaryHingePlate() {
    return $this->get('primary_hinge_plate')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setPrimaryHingePlate(TermInterface $material) {
    $this->set('primary_hinge_plate', $material->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPrimaryHingePlateId() {
    return (int) $this->get('primary_hinge_plate')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setPrimaryHingePlateId($tid) {
    $this->set('primary_hinge_plate', $tid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDoorFrameHeight() {
    return (float) $this->get('door_frame_height')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setDoorFrameHeight($height) {
    $this->set('door_frame_height', $height);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDoorFrameStockThickness() {
    return (float) $this->get('door_frame_stock_thickness')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setDoorFrameStockThickness($thickness) {
    $this->set('door_frame_stock_thickness', $thickness);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeCabinetModule($module) {
    $modules = $this->getCabinetModules();
    foreach ($modules as $index => $target_module) {
      if ($target_module->id() == $module->id()) {
        unset($modules[$index]);
      }
    };
    $this->setCabinetModules($modules);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCabinetModules() {
    return $this->get('cabinet_modules')->referencedEntities();
  }

  /**
   * {@inheritdoc}
   */
  public function setCabinetModules(array $modules) {
    $module_ids = [];
    foreach ($modules as $module) {
      $module_ids[] = $module->id();
    };
    $this->set('cabinet_modules', $module_ids);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCabinetModuleIds() {
    $module_ids = [];
    foreach ($this->getCabinetModules() as $module) {
      $module_ids[] = $module->id();
    };
    return $module_ids;
  }

  /**
   * {@inheritdoc}
   */
  public function hasCabinetModule(CabinetModuleInterface $module) {
    $module_ids = [];
    foreach ($this->getCabinetModules() as $stored_module) {
      if ($stored_module->id() == $module->id()) {
        return TRUE;
      }
    };
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function addCabinetModule(CabinetModuleInterface $module) {
    return $this->get('cabinet_modules')->appendItem($module);
  }

  /**
   * {@inheritdoc}
   */
  public function batchRebuildParts() {
    $this->batchClearComponents();
    $this->batchBuildModules();
    $this->batchPlotComponents();
    $this->batchPackSheets();
    $this->batchBuildProjectPhoto();
  }

  /**
   * {@inheritdoc}
   */
  public function batchBuildModules() {
    $batch = [
      'title' => t('Building cabinet module parts'),
      'init_message' => t('Building cabinet module parts'),
      'operations' => [],
    ];

    // Build batch modules.
    foreach ($this->getCabinetModules() as $cabinet_module) {
      /* @var $cabinet_module \Drupal\cabinetry_cabinet_project\CabinetModuleInterface */
      $batch['operations'][] = [
        [
          'Drupal\cabinetry_cabinet_project\Entity\CabinetProject',
          'buildModuleBatch',
        ],
        [$this->id(), $cabinet_module->id()],
      ];
    }
    batch_set($batch);
  }

  /**
   * {@inheritdoc}
   */
  public function batchPlotComponents() {
    $batch = [
      'title' => t('Plotting project components'),
      'init_message' => t('Preparing to plot project components'),
      'operations' => [],
    ];

    // Build batch modules.
    foreach ($this->getCabinetModules() as $cabinet_module) {
      /* @var $cabinet_module \Drupal\cabinetry_cabinet_project\CabinetModuleInterface */
      $batch['operations'][] = [
        [
          'Drupal\cabinetry_cabinet_project\Entity\CabinetProject',
          'plotModuleBatch',
        ],
        [$this->id(), $cabinet_module->id()],
      ];
    }
    batch_set($batch);
  }

  /**
   * {@inheritdoc}
   */
  public function batchBuildProjectPhoto() {
    $batch = [
      'title' => t('Collating project image from modules.'),
      'init_message' => t('Beginning to collate project image from modules.'),
      'operations' => [],
    ];

    $batch['operations'][] = [
      [
        'Drupal\cabinetry_cabinet_project\Entity\CabinetProject',
        'createProjectPhotoBatch',
      ],
      [$this->id()],
    ];

    batch_set($batch);
  }

  /**
   * {@inheritdoc}
   */
  public static function createProjectPhotoBatch($project_eid) {
    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($project_eid);
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */
    $running_x = 0;
    $max_y = 0;

    $images = [];
    foreach ($project->getprojectImages() as $module_image) {
      $uri = $module_image->getFileUri();
      $stream_wrapper_manager = \Drupal::service('stream_wrapper_manager')->getViaUri($uri);
      $file_path = $stream_wrapper_manager->realpath();

      $cur_image = imagecreatefrompng($file_path);
      $cur_image_x = imagesx($cur_image);
      $cur_image_y = imagesy($cur_image);

      $running_x = $running_x + $cur_image_x;
      $max_y = $cur_image_y > $max_y ? $cur_image_y : $max_y;
      $images[] = $cur_image;
    }

    $final_image = imagecreatetruecolor($running_x + 1, $max_y + 1);

    // Set background to white.
    $white = imagecolorallocate($final_image, 255, 255, 255);
    imagefill($final_image, 0, 0, $white);

    $running_x = 0;
    foreach ($images as $image) {
      $image_width = imagesx($image);
      $image_height = imagesy($image);
      imagecopy($final_image, $image, $running_x, $max_y - $image_height, 0, 0, $image_width, $image_height);
      $running_x = $running_x + $image_width;
    }

    imageflip($final_image, IMG_FLIP_VERTICAL);

    // uniqid() avoids caching problems with project photo filename.
    $file = ImageWriter::writePngToManaged(
      'project_photos',
      $final_image,
      $project->id() . '-' . uniqid(rand(), TRUE) . '.png'
    );

    $project->setProjectPhoto($file);
    $project->save();
  }

  /**
   * {@inheritdoc}
   */
  public function batchPackSheets() {
    $batch = [
      'title' => t('Packing project sheets parts'),
      'init_message' => t('Preparing to pack project sheets'),
      'operations' => [],
    ];

    $batch['operations'][] = [
      [
        'Drupal\cabinetry_core\Cabinetry\SheetPacker\ShelfFF\ShelfFFPacker',
        'packParts',
      ],
      [$this->id()],
    ];

    batch_set($batch);
  }

}
