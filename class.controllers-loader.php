<?php

namespace WPS;

class ControllersLoader extends \WPS {
  private static $current_query,
                 $template,
                 $_wp_page_templates,
                 $_wp_cache_expiry = 1800;

  public static function init() {
    global $wp_query;

    self::$current_query = $wp_query;

    add_filter('theme_page_templates', [__CLASS__, 'filter_wps_view_templates'], LOAD_AFTER_WP, 2);
    add_filter('template_include', [__CLASS__, 'load_cpt_archive_controller']);
  }

  public static function load_cpt_archive_controller($template) {
    self::$template = $template;
    $controller = self::load_controller();

    if(empty($controller)) return $template;
  }

  public static function filter_wps_view_templates($page_templates, $theme) {
    $current_page_templates = wp_cache_get(
      "page_templates-{$theme->cache_hash}",
      'themes'
    );

    $page_templates = is_array($current_page_templates) ?
      $current_page_templates :
      array_merge($page_templates, []);

    self::load_files_within(
      WPS_VIEWS_DIR,
      'WPS\TemplatesRecursiveFilterIterator',
      'WPS\ControllersLoader::build_wp_page_templates_array',
      false
    );

    foreach(self::$_wp_page_templates as $file => $full_path) {
      if(!preg_match('|Template Name:(.*)$|mi', file_get_contents($full_path), $header)) {
        continue;
      }

      $page_templates[$file] = _cleanup_header_comment($header[1]);
    }

    wp_cache_add(
      "page_templates-{$theme->cache_hash}",
      $page_templates,
      'themes',
      self::$_wp_cache_expiry
    );

    return $page_templates;
  }

  public static function build_wp_page_templates_array($file) {
    self::$_wp_page_templates[$file->getFilename()] = $file->getPathname();
  }

  private static function get_current_post_type() {
    if(self::is_error_page()) return self::set_error_page_post_type();

    if(self::is_blog_index()) return self::set_blog_index_post_type();

    if(self::is_homepage()) return self::set_homepage_post_type();

    $post_type = !empty(self::$current_query->query['post_type']) ?
      self::$current_query->query['post_type'] :
      self::$current_query->queried_object->post_type;

    if($post_type !== 'page') return $post_type;

    return self::get_page_template_controller_name($post_type);
  }

  private static function get_page_template_controller_name($post_type) {
    $page_template = get_post_meta(
      self::$current_query->queried_object->ID,
      '_wp_page_template',
      true
    );

    if(empty($page_template)) return $post_type;

    if(self::is_default_page($page_template, $post_type)) {
      return self::get_default_page_controller();
    }

    self::$template = WPS_VIEWS_DIR . "/$post_type/$page_template";

    $page_name = str_replace(['page-', '.php'], '', $page_template);

    return "$page_name-$post_type";
  }

  private static function load_controller() {
    $post_type = self::get_current_post_type();
    $load_path = WPS_CONTROLLERS_DIR . "/controller.{$post_type}.php";

    if(file_exists($load_path)) {
      require_once $load_path;

      $controller_name = self::get_controller_name($post_type);
      new $controller_name($post_type, self::$current_query, self::$template);

      return true;
    }

    return false;
  }

  private static function get_controller_name($post_type) {
    return str_replace(
      '-',
      '',
      implode('-', array_map('ucfirst', explode('-', $post_type)))
    ) . 'Controller';
  }

  private static function is_error_page() {
    return is_404();
  }

  private static function set_error_page_post_type() {
    $post_type = 'error-page';
    $error_page_template = WPS_VIEWS_DIR . "/page/page-error.php";

    if(file_exists($error_page_template)) {
      self::$template = $error_page_template;
    }

    return $post_type;
  }

  private static function is_blog_index() {
    return is_home();
  }

  private static function set_blog_index_post_type() {
    $post_type = 'post';
    $blog_index_template = WPS_VIEWS_DIR . "/$post_type/index-$post_type.php";

    if(file_exists($blog_index_template)) {
      self::$template = $blog_index_template;
    }

    return $post_type;
  }

  private static function is_homepage() {
    return is_front_page();
  }

  private static function set_homepage_post_type() {
    $post_type = 'homepage';
    $homepage_template = WPS_VIEWS_DIR . "/$post_type/front-page.php";

    if(file_exists($homepage_template)) {
      self::$template = $homepage_template;
    }

    return $post_type;
  }

  private static function is_default_page($page_template, $post_type) {
    return $post_type === 'page' && $page_template === 'default';
  }

  private static function get_default_page_controller() {
    self::$template = WPS_VIEWS_DIR . "/page/page-default.php";

    return 'page';
  }
}

class TemplatesRecursiveFilterIterator extends FilterIterator {
  protected $file_prefixes = ['page', 'archive', 'index'];
}
