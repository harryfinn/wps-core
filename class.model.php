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

    if(post_type_exists($this->post_type)) return;

    $this->register_custom_post_type(
      $post_type,
      wp_parse_args($cpt_args, $this->default_cpt_args)
    );

    do_action("wps_{$post_type}_cpt_registered", $this->post_type);
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

  protected function generate_cpt_labels_for($singular, $pural) {
    return [
      'name' => $pural,
      'singular_name' => $singular,
      'add_new' => 'Add New',
      'add_new_item' => "Add New $singular",
      'edit_item' => "Edit $singular",
      'new_item' => "New $singular",
      'view_item' => "View $singular",
      'search_items' => "Search $pural",
      'not_found' => "No $pural found",
      'not_found_in_trash' => "No $pural found in Trash",
      'parent_item_colon' => "Parent $singular",
      'all_items' => "All $pural",
      'archives' => "$singular Archives",
      'insert_into_item' => "Insert into $singular",
      'uploaded_to_this_item' => "Uploaded to this $singular",
      'not_found' => "No $pural found"
    ];
  }

  protected function generate_taxonomy_labels_for($singular, $pural) {
    return [
      'name' => $pural,
      'singular_name' => $singular,
      'all_items' => "All $pural",
      'parent_item' => "Parent $singular",
      'parent_item_colon' => "Parent $singular:",
      'edit_item' => "Edit $singular",
      'update_item' => "Update $singular",
      'add_new_item' => "Add New $singular",
      'new_item_name' => "New $singular Name",
      'menu_name' => $pural
    ];
  }

  protected function register_custom_taxonomies($taxonomies) {
    foreach($taxonomies as $taxonomy => $taxonomy_options) {
      register_taxonomy(
        $taxonomy,
        $taxonomy_options['post_type'],
        $taxonomy_options['taxonomy_args']
      );
    }
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
