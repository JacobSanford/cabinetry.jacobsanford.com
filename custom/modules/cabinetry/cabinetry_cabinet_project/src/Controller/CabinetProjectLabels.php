<?php

namespace Drupal\cabinetry_cabinet_project\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\cabinetry_core\PdfImage;
use Drupal\cabinetry_core\PDF_Label;

/**
 * DownloadSrtFileController object.
 */
class CabinetProjectLabels extends ControllerBase {

  /**
   * Render the PDF of labels.
   *
   * @param int $node
   *   The node ID to render.
   */
  public function serveFile($cabinetry_cabinet_project = NULL) {
    $pdf_filename = tempnam(sys_get_temp_dir(), 'FOO');
    $pdf_content = NULL;

    $num_label_rows = 20;
    $num_label_cols = 4;
    $labels_per_page = $num_label_rows * $num_label_cols;

    $pdf = new PDF_Label(
      array(
        'paper-size' => 'letter',
        'metric' => 'mm',
        'marginLeft' => 15,
        'marginTop' => 13,
        'NX' => $num_label_cols,
        'NY' => $num_label_rows,
        'SpaceX' => 5,
        'SpaceY' => 1.8,
        'width' => 45,
        'height' => 11,
        'font-size' => 7,
      )
    );

    $pdf->AddPage();

    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($cabinetry_cabinet_project);

    foreach ($project->getParts() as $part_id => $part) {
      $label_text = $part->getName();
      if (!empty($part->getNotes())) {
        $label_text = $label_text . "\n" . $part->getNotes();
      }

      $pdf->Add_Label($label_text);
      if ($part_id != 0 && $part_id % ($labels_per_page - 1) == 0) {
        $pdf->AddPage();
      }
    }

    // Reponse.
    $pdf->Output($pdf_filename);
    $output_reponse = file_get_contents($pdf_filename);

    $response = new Response($output_reponse);
    $response->headers->set('Content-Type', 'Content-type:application/pdf');
    $response->headers->set('Content-Disposition', "attachment; filename=\"{$cabinetry_cabinet_project}_labels.pdf\"");

    return $response;
  }

}
