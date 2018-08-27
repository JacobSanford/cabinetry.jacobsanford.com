<?php

namespace Drupal\cabinetry_cabinet_project;

use Drupal\taxonomy\Entity\Term;

/**
 * Defines an object to help with taxonomy operations.
 */
class TaxonomyHelper {

  /**
   * Creates the default taxonomy terms for rail stile router bits.
   */
  public static function addDefaultStandardSlideTerms() {
    $config = \Drupal::config('cabinetry_cabinet_project.taxonomy.standard_slides.default_terms');
    $slide_items = $config->get('items');

    foreach ($slide_items as $slide_data) {
      Term::create(
        [
          'parent' => [],
          'name' => $slide_data['name'],
          'vid' => 'cabinetry_standard_slides',
          'field_cabinetry_std_min_cab_dept' => $slide_data['min_depth'],
          'field_cabinetry_std_sld_cd_pro_l' => $slide_data['cabdr_prof_len'],
          'field_cabinetry_std_sld_ext_loss' => $slide_data['ext_loss'],
          'field_cabinetry_std_sld_hol_hab' => $slide_data['hol_height_above_spacer'],
          'field_cabinetry_std_sld_hol_ins' => $slide_data['hol_horiz_inset'],
          'field_cabinetry_std_sld_length' => $slide_data['length'],
          'field_cabinetry_std_sld_sug_dlen' => $slide_data['sug_dr_depth'],
          'field_cabinetry_std_sld_tot_wc' => $slide_data['tot_width_clearance'],
          'field_cabinetry_std_sld_vert_bc' => $slide_data['vert_bot_clearance'],
          'field_cabinetry_std_sld_vert_tc' => $slide_data['vert_top_clearance'],
        ]
      )->save();
    }
  }

}
