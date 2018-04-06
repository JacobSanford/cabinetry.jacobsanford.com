<?php

namespace Drupal\cabinetry_cabinet_project\Entity;

use Drupal\cabinetry_cabinet_project\CabinetModuleInterface;
use Drupal\cabinetry_cabinet_project\CabinetProjectInterface;
use Drupal\cabinetry_cabinet_project\Cabinetry\CabinetComponentPlotter;
use Drupal\cabinetry_core\Cabinetry\SheetStockTypeTerm;
use Drupal\cabinetry_core\Cabinetry\SolidStockTypeTerm;
use Drupal\cabinetry_core\Entity\CabinetryProject;
use Drupal\cabinetry_core\ImageWriter;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\File\FileSystem;
use Drupal\Core\PhpStorage\PhpStorageFactory;
use Drupal\file\Entity\File;
use Drupal\taxonomy\TermInterface;

/**
 * Defines the Cabinet Project entity.
 *
 * @ingroup cabinetry
 *
 * @ContentEntityType(
 *   id = "cabinetry_cabinet_project",
 *   label = @Translation("Cabinet Project"),
 *   base_table = "cabinetry_cabinet_project",
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\cabinetry_cabinet_project\Form\CabinetProjectForm",
 *       "edit" = "Drupal\cabinetry_cabinet_project\Form\CabinetProjectForm",
 *       "delete" = "Drupal\cabinetry_cabinet_project\Form\CabinetProjectDeleteForm",
 *     },
 *     "views_data" = "Drupal\cabinetry_cabinet_project\Entity\CabinetProjectViewsData",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\cabinetry_cabinet_project\Entity\Controller\CabinetProjectListBuilder",
 *     "access" = "Drupal\cabinetry_cabinet_project\CabinetProjectAccessControlHandler",
 *   },
 *   links = {
 *     "collection" = "/cabinetry/cabinet_project/list",
 *     "edit-form" = "/cabinetry/cabinet_project/{cabinetry_cabinet_project}/edit",
 *     "delete-form" = "/cabinetry/cabinet_project/{cabinetry_cabinet_project}/delete",
 *     "canonical" = "/cabinetry/cabinet_project/{cabinetry_cabinet_project}",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */
class CabinetProject extends CabinetryProject implements CabinetProjectInterface {

  const CABINET_PROJECT_CABINET_NAILER_HEIGHT = 75;
  const CABINET_PROJECT_CABINET_SHELF_UNDERSIZE = 6.0;

  /**
   * {@inheritdoc}
   */
  public static function buildModuleBatch($project_eid, $module_eid, array &$context = []) {
    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($project_eid);
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    $module = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_module')
      ->load($module_eid);
    /* @var $module \Drupal\cabinetry_cabinet_project\CabinetModuleInterface */

    $class = $module->getClass();
    $builder = new $class($project, $module);
    /* @var $builder \Drupal\cabinetry_cabinet_project\Cabinetry\CabinetComponent */
    $builder->build();

    foreach ($builder->parts as $cur_part) {
      $project->addPart($cur_part);
    }

    foreach ($builder->hardware as $cur_hardware_item) {
      $project->addHardwareItem($cur_hardware_item->getTerm());
    }

    $project->save();

    $context['message'] = t(
      "Built parts for project #@project_id, module #@module_id",
      [
        '@module_id' => $module->id(),
        '@project_id' => $project->id(),
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function plotModuleBatch($project_eid, $module_eid, array &$context = []) {
    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($project_eid);
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    $module = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_module')
      ->load($module_eid);
    /* @var $module \Drupal\cabinetry_cabinet_project\CabinetModuleInterface */

    $class = $module->getClass();
    $component = new $class($project, $module);
    $plotter = new CabinetComponentPlotter($component);

    $project->addProjectImage(
      $plotter->addFileToSystem(
        $plotter->plotModule(),
        'cabinetry_component_plots',
        '.png'
      )
    );

    $project->save();

    $context['message'] = t(
      "Plotted module for project #@project_id, module #@module_id",
      [
        '@module_id' => $module->id(),
        '@project_id' => $project->id(),
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // The amount to undersize the door panel.
    $fields['door_panel_undersize'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Door Panel Undersize'))
      ->setDescription(t('Enter the amount (on each side) to undersize the door panels in the Cabinet Project, to allow for fitting and expansion.'))
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
      ->setDefaultValue(2.0)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // The amount of door reveal.
    $fields['door_reveal'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Door Reveal'))
      ->setDescription(t('The carcass door reveal, in mm.'))
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
      ->setDefaultValue(2.0)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Cabinetry door frame material.
    $fields['door_frame'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Door Frame Material'))
      ->setDescription(t('The door frame material.'))
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

    // Carcass back material.
    $fields['carcass_back'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Carcass Back Material'))
      ->setDescription(t('The material to be used for the carcass back.'))
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

    // Carcass sides and top/bottom material.
    $fields['carcass_sides'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Carcass Material'))
      ->setDescription(t('The material to be used for the carcass sides and top/bottom.'))
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
          'weight' => -3,
        ]
      )
      ->setDisplayOptions(
        'form',
        [
          'type' => 'options_select',
          'weight' => -3,
        ]
      );

    // Door panel material.
    $fields['door_panel'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Door Panel Material'))
      ->setDescription(t('The material to be used for the door panel.'))
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

    // Door panel router bit.
    $fields['door_router_bit'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Door Rail/Stile Router Bit'))
      ->setDescription(t('The router bit that will be used to mill the rail/style grooves in the door frames.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'target_type' => 'taxonomy_term',
          'handler' => 'default:taxonomy_term',
          'handler_settings' => [
            'target_bundles' => [
              'cabinetry_rail_stile_router_bits' => 'cabinetry_rail_stile_router_bits',
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

    // Should the project enforce the 32mm system?
    $fields['counter_top'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Enforce 32mm System'))
      ->setDescription(t('Enforce 32mm system restrictions on carcass dimensions when auditing this project.'))
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

    // Door panel router bit.
    $fields['primary_hinge'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Project Hinges'))
      ->setDescription(t('The hinges to use for the cabinet doors.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'target_type' => 'taxonomy_term',
          'handler' => 'default:taxonomy_term',
          'handler_settings' => [
            'target_bundles' => [
              'cabinetry_hinges' => 'cabinetry_hinges',
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

    // Door panel router bit.
    $fields['primary_hinge_plate'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Project Hinge Plates'))
      ->setDescription(t('The hinge plates to use for the cabinet doors.'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'target_type' => 'taxonomy_term',
          'handler' => 'default:taxonomy_term',
          'handler_settings' => [
            'target_bundles' => [
              'cabinetry_hinge_plates' => 'cabinetry_hinge_plates',
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

    // The amount of door reveal.
    $fields['door_frame_height'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Rail / Stile Frame Height'))
      ->setDescription(t('The height of the rail/stile frame, in mm.'))
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
      ->setDefaultValue(57.15)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // The amount of door reveal.
    $fields['door_frame_stock_thickness'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Rail / Stile Stock Thickness'))
      ->setDescription(t('The thickness of the rail/stile stock for frames, in mm.'))
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
      ->setDefaultValue(19.05)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['cabinet_modules'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Cabinet Modules'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSettings(
        [
          'target_type' => 'cabinetry_cabinet_module',
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
