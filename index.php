<?php
defined('ABSPATH') or die();

/**
* Plugin Name: 829 Blog & Category Filters (Vue)
* Plugin URI: https://829llc.com
* Description: AJAX posts + shortcode
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
        'home_url' => home_url(),
        'card_style' => get_option('eight29_card_style')
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
    
    add_action('rest_api_init', function() {
      register_rest_route( 'eight29/v1', 'categories',array(
                    'methods'  => 'GET',
                    'callback' => 'get_category_list'
          ));
    });
  }

  public function options_page() {
    add_action( 'admin_menu', function() {
      $page_title = '829 Blog & Category Filters Settings';
      $menu_title = '829 Blog & Category Filters';
      $capability = 'manage_options';
      $slug = 'eight29_settings';
      $callback = 'eight29_settings_content';
      $icon = 'dashicons-admin-plugins';
      $position = 100;
  
      add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon, $position);
    });

    function eight29_settings_content() {
      echo '
      <div class="wrap">
        <h2>829 Blog & Category Filter Settings</h2>
        <form method="post" action="options.php">';
          settings_fields('eight29_settings');
          do_settings_sections('eight29_settings');
          submit_button();
      echo'
        </form>
      </div>
      ';
    }

    add_action( 'admin_init', function() {
      register_setting('eight29_settings', 'eight29_card_style');

      add_settings_section('eight29_post_settings_section', 'Post Style Settings', 'eight29_post_settings_section', 'eight29_settings');
      add_settings_field( 'eight29_card_style', 'Post Style', 'eight29_card_style', 'eight29_settings', 'eight29_post_settings_section' );
    });

    function eight29_post_settings_section() {
      echo 'Description';
    }

    function eight29_card_style() {
      $value = get_option('eight29_card_style');
      echo '<select name="eight29_card_style" id="eight29_card_style">
      <option value="card" '.($value === 'card' ? 'selected="selected"' : '""').'>Card</option>
      <option value="list" '.($value === 'list' ? 'selected="selected"' : '""').'>List</option>
      </select>';
    }
  }

  public function init() {
    $this->load_assets();
    $this->register_shortcode();
    $this->endpoints();
    $this->options_page();
  }
}

$eight29_filters = new eight29_filters();
$eight29_filters->init();
?>