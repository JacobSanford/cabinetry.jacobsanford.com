<?php

namespace Drupal\cabinetry_core;

use Drupal\taxonomy\Entity\Term;

/**
 * Defines an object to help with taxonomy operations.
 */
class TaxonomyHelper {

  /**
   * Creates the default taxonomy terms for rail stile router bits.
   */
  public static function addDefaultRailStileBigTerms() {
    $config = \Drupal::config('cabinetry_core.taxonomy.rail_stile_router_bits.default_terms');
    $bit_items = $config->get('items');

    foreach ($bit_items as $bit_data) {
      Term::create(
        [
          'parent' => [],
          'name' => $bit_data['name'],
          'vid' => 'cabinetry_rail_stile_router_bits',
          'field_cabinetry_rail_cut_depth' => $bit_data['cut_depth'],
          'field_cabinetry_rail_cut_thickne' => $bit_data['slot_thickness'],
        ]
      )->save();
    }
  }

  /**
   * Creates the default taxonomy terms for saw blades.
   */
  public static function addDefaultSawBladeTerms() {
    $config = \Drupal::config('cabinetry_core.taxonomy.saw_blades.default_terms');
    $blade_items = $config->get('items');

    foreach ($blade_items as $blade_data) {
      Term::create(
        [
          'parent' => [],
          'name' => $blade_data['name'],
          'vid' => 'cabinetry_saw_blades',
          'field_cabinetry_width' => $blade_data['cut_width'],
        ]
      )->save();
    }
  }

  /**
   * Creates the default taxonomy terms for sheet goods.
   */
  public static function addDefaultSheetGoodTerms() {
    $config = \Drupal::config('cabinetry_core.taxonomy.sheet_goods.default_terms');
    $sheet_items = $config->get('items');

    foreach ($sheet_items as $sheet_item) {
      Term::create(
        [
          'parent' => [],
          'name' => $sheet_item['name'],
          'vid' => 'cabinetry_sheet_goods',
          'field_cabinetry_width' => $sheet_item['sheet_width'],
          'field_cabinetry_height' => $sheet_item['sheet_height'],
          'field_cabinetry_depth' => $sheet_item['sheet_depth'],
          'field_cabinetry_preserve_grain' => $sheet_item['sheet_has_grain'],
          'field_cabinetry_item_cost' => $sheet_item['sheet_cost'],
        ]
      )->save();
    }
  }

  /**
   * Creates the default taxonomy terms for solid stock.
   */
  public static function addDefaultSolidStockTerms() {
    $config = \Drupal::config('cabinetry_core.taxonomy.cabinetry_solid_stock.default_terms');
    $stock_items = $config->get('items');

    foreach ($stock_items as $stock_data) {
      Term::create(
        [
          'parent' => [],
          'name' => $stock_data['name'],
          'vid' => 'cabinetry_solid_stock',
          'field_cabinetry_boardfood_cost' => $stock_data['boardfoot_cost'],
          'field_cabinetry_width' => $stock_data['stock_length'],
        ]
      )->save();
    }
  }

  /**
   * Creates the default taxonomy terms for hinges.
   */
  public static function addDefaultHingeTerms() {
    $config = \Drupal::config('cabinetry_core.taxonomy.cabinetry_hinges.default_terms');
    $hinge_items = $config->get('items');

    foreach ($hinge_items as $hinge_data) {
      Term::create(
        [
          'parent' => [],
          'name' => $hinge_data['name'],
          'vid' => 'cabinetry_hinges',
          'field_cabinetry_item_cost' => $hinge_data['cost'],
        ]
      )->save();
    }
  }

  /**
   * Creates the default taxonomy terms for hinges.
   */
  public static function addDefaultHingePlateTerms() {
    $config = \Drupal::config('cabinetry_core.taxonomy.cabinetry_hinge_plates.default_terms');
    $hinge_plate_items = $config->get('items');

    foreach ($hinge_plate_items as $hinge_plate_data) {
      Term::create(
        [
          'parent' => [],
          'name' => $hinge_plate_data['name'],
          'vid' => 'cabinetry_hinge_plates',
          'field_cabinetry_item_cost' => $hinge_plate_data['cost'],
        ]
      )->save();
    }
  }

}
