<?php

namespace Drupal\cabinetry_cabinet_project\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\cabinetry_core\PdfImage;

/**
 * DownloadSrtFileController object.
 */
class CabinetProjectPlans extends ControllerBase {

  /**
   * Render the PDF plans and serve.
   *
   * @param int $node
   *   The node ID to render.
   */
  public function serveFile($cabinetry_cabinet_project = NULL) {
    $pdf_filename = tempnam(sys_get_temp_dir(), 'FOO');
    $pdf_content = NULL;
    $pdf = new PdfImage();

    $project = \Drupal::entityTypeManager()
      ->getStorage('cabinetry_cabinet_project')
      ->load($cabinetry_cabinet_project);

    $pdf->SetTitle($project->getTitle);

    foreach ($project->getCutSheets() as $sheet) {
      foreach ($sheet->getCutSheetImages() as $sheet_image) {
        $uri = $sheet_image->getFileUri();
        $stream_wrapper_manager = \Drupal::service('stream_wrapper_manager')->getViaUri($uri);
        $file_path = $stream_wrapper_manager->realpath();

        $pdf->AddPage("L");
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(260, 10, $sheet->getName(), 0, 0, 'C');
        $pdf->centreImage($file_path, 'png');
      }
    }

    // Reponse.
    $pdf->Output($pdf_filename);
    $output_reponse = file_get_contents($pdf_filename);

    $response = new Response($output_reponse);
    $response->headers->set('Content-Type', 'Content-type:application/pdf');
    $response->headers->set('Content-Disposition', "attachment; filename=\"{$cabinetry_cabinet_project}.pdf\"");

    return $response;
  }

}
