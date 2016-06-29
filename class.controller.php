<?php

namespace WPS;

class Controller {
  public $post_type;

  public function __construct($post_type, $template_fallback) {
    $this->post_type = $post_type;

    if(!is_post_type_archive()) return $template_fallback;

  	$archive_template = WPS_VIEWS_DIR . "/{$post_type}/archive-{$post_type}.php";

    if(file_exists($archive_template)) {
      $this->index($archive_template);

      return false;
    }

    return $template_fallback;
  }

  public function index($template) {
    include $template;
  }

  public function single($template) {
    include $template;
  }
}
