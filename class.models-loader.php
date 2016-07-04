<?php

namespace WPS;

class ModelsLoader extends \WPS {
  public static function init() {
    if(file_exists(WPS_INCLUDES_DIR . '/CMB2/init.php')) {
      require_once(WPS_INCLUDES_DIR . '/CMB2/init.php');
    }

    add_action('cmb2_init', [__CLASS__, 'load_models']);
  }

  public static function load_models() {
    if(file_exists(WPS_MODELS_DIR)) {
      self::load_files_within(
        WPS_MODELS_DIR,
        'WPS\ModelRecursiveFilterIterator',
        'WPS\ModelsLoader::loader_callback'
      );
    }
  }

  public static function loader_callback($file) {
    $filter_filename = str_replace(
      ['cmb2.', 'model.', '.php'],
      '',
      $file->getFilename()
    );
    $class_name = implode(
      '-',
      array_map('ucfirst', explode('-', $filter_filename))
    );

    if(class_exists($class_name)) new $class_name;
  }
}

class ModelRecursiveFilterIterator extends FilterIterator {
  protected $file_prefixes = ['model.', 'cmb2.'];
}
