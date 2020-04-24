<?php
defined('ABSPATH') or die();

/**
* Plugin Name: 829 Filters (Vue)
* Plugin URI: https://829llc.com
* Description: 829 Filter Plugin
* Version: 1.0
* Author: Chris Roberts
* Author URI: https://829llc.com
**/

class eight29_filters {
  public function load_assets() { //Enqueue Scripts & Styles
    add_action('wp_enqueue_scripts', function() {
      wp_enqueue_style('eight29_style', plugin_dir_url(__FILE__).'/dist/assets/css/style.css', null, '1.0');
      wp_enqueue_script('eight29_assets', plugin_dir_url(__FILE__).'/dist/main.js', null, '1.0', true);
    });

    add_action('wp_enqueue_scripts', function() {
      $params = [
        'plugin_url' => plugin_dir_url(__FILE__),
        'home_url' => home_url()
      ];

      wp_localize_script('eight29_assets', 'wp', $params);
    });
  }

  public function register_shortcode() {
    add_shortcode('eight29_filters', function() {
      $code = '<div class="eight29-filters"></div>';
      return $code;
    });
  }

  public function endpoints() {
    function get_category_list() {
      $data = [];
      $args = [
        'hide_empty' => false
      ];
      $categories = get_categories($args);
      
      foreach($categories as $category) {
        $child_data = [];

        $children = get_terms([
          'hide_empty' => false,
          'taxonomy' => $category->taxonomy,
          'child_of' => $category->term_id
        ]);

        foreach ($children as $child) {
          array_push($child_data, [
            'id' => $child->term_id,
            'description' => $child->description,
            'name' => $child->name,
            'slug' => $child->slug,
            'taxonomy' => $child->taxonomy,
            'parent' => $child->parent,
            'count' => $child->count,
          ]);
        }

        $child = count($child_data) !== 0 ? $child_data : false;

        array_push($data, [
          'id' => $category->term_id,
          'description' => $category->description,
          'name' => $category->name,
          'slug' => $category->slug,
          'taxonomy' => $category->taxonomy,
          'parent' => $category->parent,
          'count' => $category->count,
          'children' => $child
        ]);
      }

      return $data;
    }
    
    add_action('rest_api_init', function () {
      register_rest_route( 'eight29/v1', 'categories',array(
                    'methods'  => 'GET',
                    'callback' => 'get_category_list'
          ));
    });
  }

  public function init() {
    $this->load_assets();
    $this->register_shortcode();
    $this->endpoints();
  }
}

$eight29_filters = new eight29_filters();
$eight29_filters->init();
?>