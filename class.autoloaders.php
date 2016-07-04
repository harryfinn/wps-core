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
    $class_file_path = self::class_file_path($class_name);

    if(file_exists($class_file_path)) require_once $class_file_path;
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

  protected static function class_file_path($filename) {
    if(strpos($filename, '\\') !== false) {
      return self::load_wps_namespaced_class($filename);
    }

    $lowercase_filename = strtolower($filename);
    $wps_core_file_path = WPS_INCLUDES_DIR . "/class.$lowercase_filename.php";

    if(file_exists($wps_core_file_path)) return $wps_core_file_path;

    return self::load_wps_app_files($lowercase_filename);
  }

  private static function load_wps_namespaced_class($filename) {
    $file_path_parts = explode('/', str_replace('\\', '/', $filename));
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

  private static function load_wps_app_files($filename) {
    $controller_file = self::load_wps_app_file_for('controller', $filename);
    if(!empty($controller_file)) return $controller_file;

    $model_file = self::load_wps_app_file_for('model', $filename);
    if(!empty($model_file)) return $model_file;

    return false;
  }

  private static function load_wps_app_file_for($type, $filename) {
    $_wps_directories = [
      'controller' => WPS_CONTROLLERS_DIR,
      'model' => WPS_MODELS_DIR
    ];
    $_filtered_filename = str_replace("-$type", '', $filename);
    $file_path = $_wps_directories[$type] . "/$type.$_filtered_filename.php";

    if(file_exists($file_path)) return $file_path;

    return false;
  }
}
