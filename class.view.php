<?php

namespace WPS;

class View {
  public $post_id,
         $field_prefix,
         $img_width,
         $img_height,
         $render_args,
         $helper;

  public $cmb_prefix = CMB2_PREFIX;

  public $field_defaults = [
    'multi'       => false,
    'multi_val'   => null,
    'is_single'   => true,
    'placeholder' => false
  ];

  public function __construct($post_id) {
    $this->post_id = $post_id;
    $this->helper = new ViewHelper(ViewHelpersLoader::init());
  }

  public function set_image_size($width, $height) {
    $this->img_width = $width;
    $this->img_height = $height;
  }

  public function field($field_name, array $field_args = null) {
    $field_args = array_merge(
                    $this->field_defaults,
                    (!empty($field_args) ? $field_args : [])
                  );

    $field_value = $this->empty_field_check(
                     $this->field_prefix . $field_name,
                     $field_args['multi'],
                     $field_args['multi_val'],
                     $field_args['is_single']
                   );

    if(strpos($field_name, 'image') !== false && empty($field_value) &&
      $field_args['placeholder'] === true) {
        if(!empty($field_args['image'])) {
          return $this->get_placeholder(
            $field_args['image']['w'],
            $field_args['image']['h']
          );
        } else {
          return $this->get_placeholder($this->img_width, $this->img_height);
        }
    } else {
      return $field_value;
    }
  }

  public function get_featured_image($size = 'full') {
    if(has_post_thumbnail($this->post_id)) {
      $featured_image = wp_get_attachment_image_src(
        get_post_thumbnail_id($this->post_id),
        $size
      );

      return $featured_image[0];
    }

    if($this->field_defaults['placeholder']) {
      return $this->generate_placeholder_from($size);
    }

    return false;
  }

  public function render($template_name, array $render_args = null) {
    $this->render_args = $render_args;

    $template_file = strpos($template_name, '-tpl.php') === false ?
      $template_name . '-tpl.php' :
      $template_name;

    $render_path = trailingslashit(WPS_VIEWS_DIR) . $template_file;

    if(file_exists($render_path)) {
      include($render_path);
    } else {
      trigger_error("No template found for: $template_file ($template_name)");
    }
  }

  public function template_exists($template) {
    return !empty(locate_template($template)) ?
            true :
            false;
  }

  public function format_content($content) {
    return apply_filters('the_content', $content);
  }

  protected function get_cmb2_field($field, $is_single = true) {
    return get_post_meta($this->post_id, $field, $is_single);
  }

  protected function empty_field_check($metabox_field, $multi = null, $multi_val = null, $is_single = true) {
    if($multi === true) {
      return !empty($this->get_cmb2_field(
               $this->cmb_prefix . $metabox_field,
               $is_single
             )) ?
             ($this->get_cmb2_field(
               $this->cmb_prefix . $metabox_field,
               $is_single
             ) == $multi_val ? true : false) :
             false;
    } else {
      return !empty($this->get_cmb2_field(
               $this->cmb_prefix . $metabox_field,
               $is_single
             )) ?
               $this->get_cmb2_field(
               $this->cmb_prefix . $metabox_field,
               $is_single
             ) :
             '';
    }
  }

  protected function get_placeholder($width, $height) {
    return 'http://placehold.it/' . $width . 'x' . $height;
  }

  protected function get_post_object($post_id = null) {
    return get_post(!empty($post_id) ? $post_id : $this->post_id);
  }

  protected function generate_placeholder_from($size) {
    global $_wp_additional_image_sizes;
    $placeholder_size = $size == 'full' ? 'post-thumbnail' : $size;
    $width = $_wp_additional_image_sizes[$placeholder_size]['width'];
    $height = $_wp_additional_image_sizes[$placeholder_size]['height'];

    return $this->get_placeholder($width, $height);
  }
}
