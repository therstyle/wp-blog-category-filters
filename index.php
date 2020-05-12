<?php
defined('ABSPATH') or die();

/**
* Plugin Name: 829 Blog & Category Filters
* Plugin URI: https://829llc.com
* Description: Display blog index with category filtering with shortcode [eight29_filters]
* Version: 1.0
* Author: Chris Roberts
* Author URI: https://829llc.com
**/

class eight29_filters {
  public static function plugin_activated() {
    function activation() {
      do_action( 'eight29_filters_default_options' );
    }

    register_activation_hook( __FILE__, 'activation' );
    add_action( 'eight29_filters_default_options', function() {
      add_option('eight29_post_style', 'PostCard');
      add_option('eight29_post_per_page', '10');
      add_option('eight29_post_per_row', '1');
      add_option('eight29_featured_image', 'true');
      add_option('eight29_featured_image_size', 'medium');
      add_option('eight29_sidebar', 'true');
      add_option('eight29_author', 'true');
      add_option('eight29_date', 'true');
      add_option('eight29_categories', 'true');
      add_option('eight29_post_counts', 'false');
    });
  }

  public static function load_assets() { //Enqueue Scripts & Styles
    add_action('wp_enqueue_scripts', function() {
      wp_enqueue_style('eight29_style', plugin_dir_url(__FILE__).'/dist/assets/css/style.css', null, '1.0');
      wp_enqueue_script('eight29_assets', plugin_dir_url(__FILE__).'/dist/main.js', null, '1.0', true);
    });

    add_action('wp_enqueue_scripts', function() {
      $params = [
        'plugin_url' => plugin_dir_url(__FILE__),
        'home_url' => home_url(),
        'post_style' => get_option('eight29_post_style'),
        'post_per_page' => get_option('eight29_post_per_page'),
        'post_per_row' => get_option('eight29_post_per_row'),
        'display_featured_image' =>  get_option('eight29_featured_image') === 'true' ? true : false,
        'display_featured_image_size' => get_option('eight29_featured_image_size'),
        'display_sidebar' =>  get_option('eight29_sidebar') === 'true' ? true : false,
        'display_author' =>  get_option('eight29_author') === 'true' ? true : false,
        'display_date' =>  get_option('eight29_date') === 'true' ? true : false,
        'display_categories' =>  get_option('eight29_categories') === 'true' ? true : false,
        'display_post_counts' =>  get_option('eight29_post_counts') === 'true' ? true : false
      ];

      wp_localize_script('eight29_assets', 'wp', $params);
    });
  }

  public static function register_shortcode() {
    add_shortcode('eight29_filters', function() {
      $code = '<div class="eight29-filters"></div>';
      return $code;
    });
  }

  public function endpoints() {
    function get_cat_children($taxonomy, $id) {
      $child_data = [];

      $children = get_terms([
        'hide_empty' => false,
        'taxonomy' => $taxonomy,
        'child_of' => $id
      ]);

      if ($children) {
        foreach ($children as $child) {
          array_push($child_data, [
            'id' => $child->term_id,
            'description' => $child->description,
            'name' => $child->name,
            'slug' => $child->slug,
            'taxonomy' => $child->taxonomy,
            'parent' => $child->parent,
            'count' => $child->count,
            'children' => get_cat_children($child->taxonomy, $child->term_id)
          ]);
        }
      }

      return $child_data;
    }

    function get_category_list() {
      $data = [];
      $args = [
        'hide_empty' => false
      ];
      $categories = get_categories($args);
      
      foreach($categories as $category) {
        array_push($data, [
          'id' => $category->term_id,
          'description' => $category->description,
          'name' => $category->name,
          'slug' => $category->slug,
          'taxonomy' => $category->taxonomy,
          'parent' => $category->parent,
          'count' => $category->count,
          'children' => get_cat_children($category->taxonomy, $category->term_id)
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

    //Format date into something usable
    add_action('rest_api_init', function() {
      register_rest_field(
          array('post'),
          'formatted_date',
          array(
              'get_callback'    => function() {
                  return get_the_date();
              },
              'update_callback' => null,
              'schema'          => null,
          )
      );
    });

    //Add featured image srcset to rest API
    add_action('rest_api_init', function() {
      register_rest_field(
          array('post'),
          'featured_image_srcset',
          array(
              'get_callback'    => function($object, $field_name, $request) {
                  $data = wp_get_attachment_image_srcset($object['featured_media'], get_option('eight29_featured_image_size'));
                  return $data;
              },
              'update_callback' => null,
              'schema'          => null,
          )
      );
    });
  }

  public static function options_page() {
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
      function get_the_sizes() {
        $values = [];
        $sizes = get_intermediate_image_sizes();

        foreach($sizes as $size) {
          $values[$size] = $size;
        }
        
        return $values;
      }

      $fields = [
        [
          'id' => 'eight29_sidebar',
          'type' => 'select',
          'label' => 'Display Categories Sidebar?',
          'section' => 'eight29_settings',
          'default' => 'true',
          'options' => [
            'Yes' => 'true',
            'No' => 'false',
          ]
        ],
        [
          'id' => 'eight29_post_counts',
          'type' => 'select',
          'label' => 'Display Post Counts?',
          'section' => 'eight29_settings',
          'default' => 'false',
          'options' => [
            'Yes' => 'true',
            'No' => 'false',
          ]
        ],
        [
          'id' => 'eight29_post_per_page',
          'type' => 'number',
          'label' => 'Posts Per Page',
          'section' => 'eight29_settings',
          'default' => '10',
          'max' => '50'
        ],
        [
          'id' => 'eight29_post_per_row',
          'type' => 'number',
          'label' => 'Posts Per Row',
          'section' => 'eight29_settings',
          'default' => '1',
          'max' => '4'
        ],
        [
          'id' => 'eight29_post_style',
          'type' => 'select',
          'label' => 'Posts Style',
          'section' => 'eight29_settings',
          'default' => 'PostCard',
          'options' => [
            'Card' => 'PostCard',
            'List' => 'PostList'
          ]
        ],
        [
          'id' => 'eight29_featured_image',
          'type' => 'select',
          'label' => 'Display Featured Image?',
          'section' => 'eight29_settings',
          'default' => 'true',
          'options' => [
            'Yes' => 'true',
            'No' => 'false',
          ]
        ],
        [
          'id' => 'eight29_featured_image_size',
          'type' => 'select',
          'label' => 'Featured Image Size',
          'section' => 'eight29_settings',
          'default' => 'true',
          'options' => get_the_sizes()
        ],
        [
          'id' => 'eight29_author',
          'type' => 'select',
          'label' => 'Display Author Name?',
          'section' => 'eight29_settings',
          'default' => 'true',
          'options' => [
            'Yes' => 'true',
            'No' => 'false',
          ]
        ],
        [
          'id' => 'eight29_date',
          'type' => 'select',
          'label' => 'Display Post Date?',
          'section' => 'eight29_settings',
          'default' => 'true',
          'options' => [
            'Yes' => 'true',
            'No' => 'false',
          ]
        ],
        [
          'id' => 'eight29_categories',
          'type' => 'select',
          'label' => 'Display Post Categories?',
          'section' => 'eight29_settings',
          'default' => 'true',
          'options' => [
            'Yes' => 'true',
            'No' => 'false',
          ]
        ],
      ];

      //Sections
      add_settings_section('eight29_post_settings_section', 'Post Style Settings', 'eight29_post_settings_section', 'eight29_settings');

      //Register & Add Settings
      foreach ($fields as $field) {
        register_setting($field['section'], $field['id']);
        add_settings_field($field['id'], $field['label'], 'field_callback', $field['section'], 'eight29_post_settings_section', $field);
      }
    });

    function eight29_post_settings_section() {
      //front end description text
    }

    function field_callback($field) {      
      $value = get_option($field['id']);

      if ($field['type'] === 'select') {
        echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
          foreach ($field['options'] as $label => $option_value) {
            echo '<option value="'.$option_value.'" '.($option_value === $value ? 'selected="selected"' : null).'>'.$label.'</option>';
          }
        echo '</select>';
      }

      elseif ($field['type'] === 'number' || $field['type'] === 'text') {
        $max = $field['max'] ? 'max="'.$field['max'].'"' : null;
        echo '<input name="'.$field['id'].'" id="'.$field['id'].'" type="'.$field['type'].'" value="'.$value.'" '.$max.'>';
      }
    }
  }

  public static function init() {
    self::plugin_activated();
    self::load_assets();
    self::register_shortcode();
    self::endpoints();
    self::options_page();
  }
}

eight29_filters::init();
?>