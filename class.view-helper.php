<?php

namespace WPS;

class ViewHelper {
  private $_helper_classes;

  public function __construct($helper_classes) {
    $this->_helper_classes = $helper_classes;
  }

  public function __call($method_name, $method_args) {
    foreach($this->_helper_classes as $helper_class => $class_obj) {
      if(method_exists($class_obj, $method_name)) {
        return call_user_func_array([$class_obj, $method_name], $method_args);
      }
    }

    trigger_error("No helper class method found for: $method_name");
  }
}
