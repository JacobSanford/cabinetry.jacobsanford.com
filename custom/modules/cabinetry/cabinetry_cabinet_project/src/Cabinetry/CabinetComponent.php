<?php

namespace Drupal\cabinetry_cabinet_project\Cabinetry;

use Drupal\cabinetry_core\Cabinetry\EdgeBanding;
use Drupal\taxonomy\TermInterface;

/**
 * A generic object to serve as a module in a cabinetry cabinet project.
 */
class CabinetComponent {

  /**
   * The outer width (x) of the component, in millimeters.
   *
   * @var float
   */
  public $width = 0.0;

  /**
   * The outer height (y) of the component, in millimeters.
   *
   * @var float
   */
  public $height = 0.0;

  /**
   * The depth (z) of the component, in millimeters.
   *
   * @var float
   */
  public $depth = 0.0;

  /**
   * An array of CabinetHardwareItem objects comprising this component.
   *
   * @var array
   */
  public $hardware = [];

  /**
   * An array of materials and lengths of edge banding.
   *
   * @var array
   */
  public $banding = [];

  /**
   * An array of CabinetryPart objects comprising this component.
   *
   * @var \Drupal\cabinetry_core\CabinetryPartInterface[]
   */
  public $parts = [];

  /**
   * A label for this part.
   *
   * @var string
   */
  public $label = NULL;

  /**
   * Constructor.
   */
  public function __construct() {
    // Pass.
  }

  /**
   * Add edge banding to the project materials list.
   *
   * @param array $banding_array
   *   An array of EdgeBanding objects.
   */
  public function addBandingArray(array $banding_array) {
    foreach ($banding_array as $banding_object) {
      $object_found = FALSE;
      foreach ($this->banding as $banding_index => $banding_value) {
        if (
          $banding_value->material == $banding_object->material &&
          $banding_value->width == $banding_object->width
        ) {
          $this->banding[$banding_index]->add($banding_object->length);
          $object_found = TRUE;
        }
      }
      if ($object_found == FALSE) {
        $this->banding[] = new EdgeBanding($banding_object->material, $banding_object->width);
        $this->banding[count($this->banding) - 1]->add($banding_object->length);
      }
    }
  }

  /**
   * Build the cabinet and determine the parts required.
   */
  public function build() {
  }

  /**
   * Plot this component in the project view.
   */
  public function plotModule($plotter) {
    $sheet_color = imagecolorallocate($plotter->canvas, 0, 0, 0);

    imagerectangle(
      $plotter->canvas,
      $plotter->padding,
      $plotter->padding,$this->width - 1,
      $plotter->padding + $this->height - 1,
      $sheet_color
    );
  }

  /**
   * Add a length of edge banding to the cabinet materials list.
   *
   * @param \Drupal\taxonomy\TermInterface $material
   *   The CabinetrySheetGood object to edge band.
   * @param float $length
   *   The length required, in millimeters.
   */
  protected function addBanding(TermInterface $material, $length) {
    $object_found = FALSE;
    foreach ($this->banding as $banding_index => $banding_value) {
      if (
        $banding_value->material == $material
      ) {
        $this->banding[$banding_index]->add($length);
        $object_found = TRUE;
      }
    }
    if ($object_found == FALSE) {
      $this->banding[] = new EdgeBanding($material);
      $this->banding[count($this->banding) - 1]->add($length);
    }
  }

  /**
   * Add additional parts to this project.
   *
   * @param \Drupal\taxonomy\TermInterface[] $parts
   *   An array of taxonomy term objects.
   */
  protected function addParts(array $parts) {
    $this->parts = array_merge($this->parts, $parts);
  }

  /**
   * Add additional parts to this project.
   *
   * @param \Drupal\taxonomy\TermInterface[] $hardware
   *   An array of taxonomy term objects.
   */
  protected function addHardware(array $hardware) {
    $this->hardware = array_merge($this->hardware, $hardware);
  }

}
