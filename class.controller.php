<?php

namespace WPS;

class Controller {
  public $post_type,
         $current_query,
         $template,
         $request_action,
         $request_data;

  public function __construct($post_type, $current_query = null, $template) {
    $this->post_type = $post_type;
    $this->current_query = $this->set_current_query($current_query);
    $this->template = $template;
    $this->set_request_action();
    $this->render_post_route_template($template);
  }

  public function index() {
    include $this->template;
  }

  public function single() {
    include $this->template;
  }

  public function create() {}

  private function render_post_route_template($template) {
    if(!empty($this->request_action) && $this->request_action !== 'single') {
      if(method_exists($this, $this->request_action)) {
        $this->{$this->request_action}();

        return;
      }
    }

    if(!$this->is_index_of_post_type()) {
      $this->render_single_template($template);

      return;
    }

  	$this->render_index_template($template);
  }

  private function set_request_action() {
    $request_method = !empty($_SERVER['REQUEST_METHOD']) ?
      $_SERVER['REQUEST_METHOD'] :
      'GET';

    $request_actions = [
      'POST' => 'create',
      'GET' => 'single'
    ];

    $this->request_data = !empty($_REQUEST) ? $_REQUEST : false;

    $this->request_action = array_key_exists($request_method, $request_actions) ?
      $request_actions[$request_method] :
      false;
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
        $this->template = $single_template;
        $this->single();

        return;
      }
    }

    $this->single();

    return;
  }

  private function render_index_template($template) {
    $archive_template = WPS_VIEWS_DIR .
      "/{$this->post_type}/{$this->index_template_prefix}-{$this->post_type}" .
      '.php';

    if(file_exists($archive_template)) {
      $this->template = $archive_template;
      $this->index();

      return;
    }

    $this->index();
  }

  private function set_current_query($query) {
    if(!empty($current_query)) return $query;

    global $wp_query;

    return $wp_query;
  }
}
