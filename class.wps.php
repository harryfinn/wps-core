<?php

class WPS {
  final public static function load_wps_core() {
    new WPS\Controllers();
    new WPS\Views();
    WPS\ModelsLoader::init();
  }

  protected static function load_files_within($dir, $filter_iterator, $callback = null) {
    $dir_files = new RecursiveDirectoryIterator(
      $dir,
      RecursiveDirectoryIterator::SKIP_DOTS
    );

    $filtered_files = new $filter_iterator($dir_files);

    $files_iterator = new RecursiveIteratorIterator(
      $filtered_files,
      RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach($files_iterator as $file) {
      if($file->isFile()) {
        require_once($file->getPathname());

        if(!empty($callback)) $callback($file->getFilename());
      }
    }
  }
}
