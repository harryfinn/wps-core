<?php

namespace WPS;

class Controller {
  public $post_type;

  public function __construct($post_type, $template_fallback) {
    $this->post_type = $post_type;

    if(!is_post_type_archive()) {
      $this->fallback_template($template_fallback);

      return;
    }

  	$archive_template = WPS_VIEWS_DIR . "/{$post_type}/archive-{$post_type}.php";

    if(file_exists($archive_template)) {
      $this->index($archive_template);

      return;
    }

    $this->fallback_template($template_fallback);
  }

  public function index($template) {
    include $template;
  }

  public function single($template) {
    include $template;
  }

  public function fallback_template($template) {
    include $template;
  }
}
