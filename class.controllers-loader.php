<?php

namespace WPS;

class ControllersLoader {
  public static function init() {
    add_filter('template_include', [__CLASS__, 'load_cpt_archive_controller']);
  }

  public static function load_cpt_archive_controller($template) {
    $controller = self::load_controller(get_query_var('post_type'), $template);

    if(empty($controller)) return $template;
  }

  private static function load_controller($post_type, $template_fallback) {
    $load_path = WPS_CONTROLLERS_DIR . "/controller.{$post_type}.php";

    if(file_exists($load_path)) {
      require_once $load_path;

      $controller_name = ucwords($post_type) . 'Controller';
      new $controller_name($post_type, $template_fallback);

      return true;
    }

    return false;
  }
}
