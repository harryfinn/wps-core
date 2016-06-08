<?php

namespace WPS;

class Model {
  public $post_type;
  public $default_cpt_args = [
    'public' => false,
    'show_ui' => true,
    'show_in_nav_menus' => true,
    'capability_type' => 'post',
    'supports' => [
      'title',
      'editor',
      'thumbnail',
      'page-attributes'
    ]
  ];

  public function __construct($post_type, array $cpt_args = []) {
    $this->post_type = $post_type;

    $this->register_custom_post_type(
      $post_type,
      wp_parse_args($cpt_args, $this->default_cpt_args)
    );
  }

  private function register_custom_post_type($cpt, $cpt_args) {
    register_post_type($cpt, $cpt_args);
  }
}
