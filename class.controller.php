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

  private function render_post_route_template($template) {
    if(!is_post_type_archive()) {
      $this->render_single_template($template);

      return;
    }

  	$this->render_archive_template($template);
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

  private function render_archive_template($template) {
    $archive_template = WPS_VIEWS_DIR .
      "/{$this->post_type}/archive-{$this->post_type}.php";

    if(file_exists($archive_template)) {
      $this->index($archive_template);

      return;
    }

    $this->single($template);
  }

  public function index($template) {
    $this->render_template($template);
  }

  public function single($template) {
    $this->render_template($template);
  }

  private function render_template($template) {
    include $template;
  }

  private function set_current_query($query) {
    if(!empty($current_query)) return $query;

    global $wp_query;

    return $wp_query;
  }
}
