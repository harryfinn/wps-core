<?php

namespace WPS;

class Controller {
  public $post_type,
         $current_query;

  public function __construct($post_type, $current_query = null, $template) {
    $this->post_type = $post_type;
    $this->current_query = $this->set_current_query($current_query);
    $this->render_post_route_template($template);
  }

  public function index($template) {
    include $template;
  }

  public function single($template) {
    include $template;
  }

  private function render_post_route_template($template) {
    if(!$this->is_index_of_post_type()) {
      $this->render_single_template($template);

      return;
    }

  	$this->render_index_template($template);
  }

  private function is_index_of_post_type() {
    $is_blog_index = is_home();
    $this->index_template_prefix = $is_blog_index ? 'index' : 'archive';

    return is_post_type_archive() || $is_blog_index;
  }

  private function render_single_template($template) {
    if(is_single()) {
      $single_template = WPS_VIEWS_DIR .
        "/{$this->post_type}/single-{$this->post_type}.php";

      if(file_exists($single_template)) {
        $this->single($single_template);

        return;
      }
    }

    $this->single($template);

    return;
  }

  private function render_index_template($template) {
    $archive_template = WPS_VIEWS_DIR .
      "/{$this->post_type}/{$this->index_template_prefix}-{$this->post_type}" .
      '.php';

    if(file_exists($archive_template)) {
      $this->index($archive_template);

      return;
    }

    $this->index($template);
  }

  private function set_current_query($query) {
    if(!empty($current_query)) return $query;

    global $wp_query;

    return $wp_query;
  }
}
