<?php

namespace WPS;

class Autoloaders {
  public static function init() {
    spl_autoload_register([__CLASS__, 'autoload_lib_classes']);
    spl_autoload_register([__CLASS__, 'autoload_classes']);

    if(function_exists('__autoload')) {
      spl_autoload_register([__CLASS__, '__autoload']);
    }
  }

  public static function autoload_classes($name) {
    $class_name = self::format_class_filename($name);
    $class_path = self::class_file_path($class_name);

    if(file_exists($class_path)) require_once $class_path;
  }

  public static function autoload_lib_classes($name) {
    $lib_class_name = self::class_file_path($name);

    if(file_exists($lib_class_name)) require_once($lib_class_name);
  }

  protected static function format_class_filename($filename) {
    return strtolower(
      implode(
        '-',
        preg_split('/(?=[A-Z])/', $filename, -1, PREG_SPLIT_NO_EMPTY)
      )
    );
  }

  protected static function class_file_path($filename, $dir = __DIR__) {
    if(strpos($filename, '\\') !== false) {
      return self::load_namespaced_class($filename);
    }

    return $dir . '/class.' . strtolower($filename) . '.php';
  }

  private static function load_namespaced_class($file_path) {
    $file_path_parts = explode('/', str_replace('\\', '/', $file_path));
    $file_path_keys = array_keys($file_path_parts);
    $class_filename_key = end($file_path_keys);
    $class_filename = self::format_class_filename(
      $file_path_parts[$class_filename_key]
    );
    $file_path_parts[$class_filename_key] = 'class.' . $class_filename  . '.php';

    return trailingslashit(dirname(WPS_INCLUDES_DIR)) . strtolower(
      implode($file_path_parts, '/')
    );
  }
}
