<?php

namespace Drupal\cabinetry_core\Cabinetry\SheetPacker;

use Drupal\cabinetry_core\Cabinetry\SheetPlotter\SheetPlotter;
use Drupal\cabinetry_core\CabinetryProjectInterface;
use Drupal\cabinetry_core\StockItemInterface;
use Drupal\file\Entity\File;

/**
 * A generic object for packing a sheet good from a piece list.
 */
class SheetPacker {

  /**
   * The blade cut width to allow for, in millimeters.
   *
   * @var float
   */
  protected $cutWidth = 0.0;

  /**
   * Does this sheet have grain that should be vertical?
   *
   * @var bool
   */
  protected $hasGrain = TRUE;

  /**
   * The height (y) of the sheet good, in millimeters.
   *
   * @var float
   */
  protected $height = 0.0;

  /**
   * A list of pieces to sort into sheets.
   *
   * @var \Drupal\cabinetry_core\CabinetryPartInterface[]
   */
  protected $parts = [];

  /**
   * A list of sheets generated by the packing.
   *
   * @var \Drupal\cabinetry_core\Cabinetry\SheetPacker\PackedSheet[]
   */
  protected $sheets = [];

  /**
   * The width (x) of the sheet good, in millimeters.
   *
   * @var float
   */
  protected $width = 0.0;

  /**
   * The parent project entity.
   *
   * @var \Drupal\cabinetry_core\CabinetryProjectInterface
   */
  protected $project = NULL;

  /**
   * The sheet stock source.
   *
   * @var \Drupal\cabinetry_core\StockItemInterface
   */
  protected $stock = NULL;

  /**
   * Constructor.
   *
   * @param \Drupal\cabinetry_core\CabinetryProjectInterface $project
   *   The project to store the sheets in.
   * @param \Drupal\cabinetry_core\StockItemInterface $stock_item
   *   The stock source of the sheet.
   * @param \Drupal\cabinetry_core\CabinetryPartInterface[] $parts
   *   Parts to pack into sheets.
   */
  protected function __construct(CabinetryProjectInterface $project, StockItemInterface $stock_item, array $parts) {
    $this->project = $project;
    $this->stock = $stock_item;
    $this->parts = $parts;
    $this->width = $this->stock->getWidth();
    $this->height = $this->stock->getHeight();
    $this->depth = $this->stock->getDepth();
    $this->hasGrain = $this->stock->getPreserveGrain();
    $this->cutWidth = $this->project->getSawBladeCutWidth();
  }

  /**
   * Constructor.
   *
   * @param int $project_eid
   *   The entity ID of the project.
   * @param array $context
   *   The batch API context associative array for the batch.
   */
  public static function packParts($project_eid, array &$context = []) {
    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($project_eid);
    /* @var $project \Drupal\cabinetry_cabinet_project\CabinetProjectInterface */

    // Collate parts before packing sheets.
    $parts = [];
    foreach ($project->getParts() as $part) {
      /* @var $part \Drupal\cabinetry_core\CabinetryPartInterface */
      $stock_source_id = $part->getStockSourceId();

      if (!isset($parts[$stock_source_id])) {
        $parts[$stock_source_id] = [];
      }
      $parts[$stock_source_id][] = $part;
    }

    // Pack each stock source separately.
    foreach ($parts as $stock_source_id => $part_list) {
      $stock_item = \Drupal::entityTypeManager()
        ->getStorage('cabinetry_stock_item')
        ->load($stock_source_id);
      /* @var $stock_item \Drupal\cabinetry_core\StockItemInterface */

      $obj = new static(
        $project,
        $stock_item,
        $part_list
      );

      // Plot the cut sheet images.
      $cut_sheet_images = [];
      foreach ($obj->sheets as $cur_sheet) {
        $plotter = new SheetPlotter($cur_sheet);
        $image_path = $plotter->plotSheet();
        $cut_sheet_images[] = $obj->addFileToSystem($image_path, '.png');
      }

      // Create the cut sheet entity.
      $cut_sheet_entity_data = [
        'type' => 'cabinetry_cut_sheet',
        'name' => $stock_item->getName(),
        'material' => $stock_item->getMaterialId(),
        'cut_sheet_images' => $cut_sheet_images,
        'width' => $stock_item->getWidth(),
        'height' => $stock_item->getHeight(),
        'depth' => $stock_item->getDepth(),
      ];

      $cut_sheet_entity = \Drupal::entityTypeManager()
        ->getStorage('cabinetry_cut_sheet')
        ->create($cut_sheet_entity_data);
      /* @var $cut_sheet_entity \Drupal\cabinetry_core\CutSheetInterface */
      $cut_sheet_entity->save();

      // Add the sheet to the project.
      $project->addCutSheet($cut_sheet_entity);
      $project->save();

      unset($obj);
    }

    $context['message'] = t(
      "Packed sheets for @project_name",
      [
        '@project_name' => $project->getName(),
      ]
    );
  }

  /**
   * Add a file from the disk to the filesystem.
   *
   * @param string $source
   *   The path to the file on the disk.
   * @param string $add_extension
   *   The extension to add to the file after copying to the filesystem.
   *
   * @return mixed
   *   The file object if the file was added, FALSE otherwise.
   */
  protected static function addFileToSystem($source, $add_extension = NULL) {
    $basename = basename($source);

    $dir = "public://cut_sheet_images";
    file_prepare_directory($dir, FILE_CREATE_DIRECTORY);
    $destination = "$dir/$basename$add_extension";

    if (file_exists($source)) {
      $uri = file_unmanaged_copy($source, $destination, FILE_EXISTS_REPLACE);
      $file = File::Create([
        'uri' => $uri,
      ]);
      $file->setPermanent();
      $file->save();
      return $file;
    }
    else {
      return FALSE;
    }
  }

}