<?php

namespace WPS;

class Model {
  public $post_type,
         $default_cpt_args = [
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
         ],
         $default_query_args = [
           'paged' => 1,
           'post_status' => 'publish'
         ],
         $_wpdb;

  public function __construct($post_type, array $cpt_args = []) {
    $this->post_type = $post_type;

    $this->register_custom_post_type(
      $post_type,
      wp_parse_args($cpt_args, $this->default_cpt_args)
    );
  }

  public function query(array $query_args = null) {
    $this->default_query_args['post_type'] = $this->post_type;
    $this->default_query_args['posts_per_page'] = get_option('posts_per_page');

    $args = array_merge(
      $this->default_query_args,
      (!empty($query_args) ? $this->set_paged_value($query_args) : [])
    );

    return new \WP_Query($args);
  }

  public function wpdb() {
    if(!empty($this->_wpdb)) return $this->_wpdb;

    $this->_wpdb = $GLOBALS['wpdb'];

    return $this->_wpdb;
  }

  private function register_custom_post_type($cpt, $cpt_args) {
    register_post_type($cpt, $cpt_args);
  }

  private function set_paged_value(array $query_args) {
    $default_paged = !empty($query_args['paged']) ?
      $query_args['paged'] :
      $this->default_query_args['paged'];

    $paged_query_var = get_query_var('paged');
    $query_args['paged'] = $paged_query_var ? $paged_query_var : $default_paged;

    return $query_args;
  }
}
