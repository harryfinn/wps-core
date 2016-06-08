<?php

namespace WPS;

class Autoloaders {
  public function __construct() {
    spl_autoload_register([$this, 'autoload_lib_classes']);
    spl_autoload_register([$this, 'autoload_classes']);

    if(function_exists('__autoload')) {
      spl_autoload_register([$this, '__autoload']);
    }
  }

  public function autoload_classes($name) {
    $class_name = $this->format_class_filename($name);
    $class_path = $this->class_file_path($class_name);

    if(file_exists($class_path)) require_once $class_path;
  }

  public function autoload_lib_classes($name) {
    $lib_class_name = $this->class_file_path($name);

    if(file_exists($lib_class_name)) require_once($lib_class_name);
  }

  protected function format_class_filename($filename) {
    return strtolower(
      implode(
        '-',
        preg_split('/(?=[A-Z])/', $filename, -1, PREG_SPLIT_NO_EMPTY)
      )
    );
  }

  protected function class_file_path($filename, $dir = __DIR__) {
    if(strpos($filename, '\\') !== false) {
      return $this->load_namespaced_class($filename);
    }

    return $dir . '/class.' . strtolower($filename) . '.php';
  }

  private function load_namespaced_class($file_path) {
    $file_path_parts = explode('/', str_replace('\\', '/', $file_path));
    $file_path_keys = array_keys($file_path_parts);
    $class_filename_key = end($file_path_keys);
    $class_filename = $this->format_class_filename(
      $file_path_parts[$class_filename_key]
    );
    $file_path_parts[$class_filename_key] = 'class.' . $class_filename  . '.php';

    return trailingslashit(dirname(WPS_INCLUDES_DIR)) . strtolower(
      implode($file_path_parts, '/')
    );
  }
}
