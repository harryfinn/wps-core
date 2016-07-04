<?php

require_once(WPS_INCLUDES_DIR . '/class.autoloaders.php');

class WPS {
  public static function init() {
    self::load_wps_core();
  }

  private static function load_wps_core() {
    WPS\Autoloaders::init();
    WPS\ModelsLoader::init();
    WPS\ControllersLoader::init();
  }

  public static function load_files_within($dir, $filter_iterator, $callback = null, $load_file = true) {
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
        if($load_file) require_once($file->getPathname());

        if(!empty($callback)) $callback($file);
      }
    }
  }
}
