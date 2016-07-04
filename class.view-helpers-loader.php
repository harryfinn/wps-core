<?php

namespace WPS;

class ViewHelpersLoader extends \WPS {
  private static $_helper_classes = [];

  public static function init() {
    if(file_exists(WPS_HELPERS_DIR)) {
      self::load_files_within(
        WPS_HELPERS_DIR,
        'WPS\ViewHelpersRecursiveFilterIterator',
        'WPS\ViewHelpersLoader::helpers_loader_callback'
      );
    }

    return self::$_helper_classes;
  }

  public static function helpers_loader_callback($file) {
    $filter_filename = str_replace(['class.', '.php'], '', $file->getFilename());
    $class_name = str_replace('-', '', implode(
      '-',
      array_map('ucfirst', explode('-', $filter_filename))
    ));

    if(class_exists($class_name)) {
      self::$_helper_classes[$class_name] = new $class_name;
    }
  }
}

class ViewHelpersRecursiveFilterIterator extends FilterIterator {
  protected $file_prefixes = ['class.'];
}
