<?php

namespace Drupal\cabinetry_core;

use Drupal\file\Entity\File;

/**
 * Defines an object to help with writing images to the filesystem.
 */
class ImageWriter {

  /**
   * Writes an on disk image to the managed filesystem.
   *
   * @param string $directory
   *   The directory name.
   * @param object $image
   *   The PHP image resource identifier.
   * @param object $filename
   *   The filename to write.
   *
   * @return mixed
   *   The created \Drupal\file\Entity\File, or FALSE if file does not exist.
   */
  public static function writePngToManaged($directory, $image, $filename) {
    $source = tempnam(sys_get_temp_dir(), $directory);
    imagepng($image, $source);
    imagedestroy($image);

    $dir = "public://$directory";
    file_prepare_directory($dir, FILE_CREATE_DIRECTORY);
    $destination = "$dir/$filename";

    if (file_exists($source)) {
      $uri = file_unmanaged_copy($source, $destination, FILE_EXISTS_REPLACE);
      $file = File::Create([
        'uri' => $uri,
      ]);
      $file->setPermanent();
      $file->save();
      return $file;
    }

    return FALSE;
  }

}
