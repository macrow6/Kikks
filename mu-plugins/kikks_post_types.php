<?php

//Events Post Type
function kikks_post_types() {
  register_post_type('event', array(
    'capability_type' => 'event',
    'has_archive' => true,
    'supports' => array('title', 'thumbnail'),
    'rewrite' => array('slug' => 'events'),
    'public' => true,
    'labels' => array(
      'name' => 'Events',
      'add_new_item' => 'Add New Event',
      'edit_item' => 'Edit Event',
      'all_items' => 'All Events',
      'singular_name' => 'Event'

    ),
    'menu_icon' => 'dashicons-calendar-alt'
  ));
//Characters Post Type
  register_post_type('character', array(
    'has_archive' => true,
    'supports' => array('title', 'thumbnail'),

    'public' => true,
    'labels' => array(
      'name' => 'Characters',
      'add_new_item' => 'Add New Character',
      'edit_item' => 'Edit Character',
      'all_items' => 'All Characters',
      'singular_name' => 'Character'

    ),
    'menu_icon' => 'dashicons-universal-access-alt'
  ));
  //Theme Post Type
    register_post_type('theme', array(
      'capability_type' => 'theme',
      'has_archive' => true,
      'supports' => array('title', 'thumbnail'),
      'rewrite' => array('slug' => 'theme'),
      'public' => true,
      'labels' => array(
        'name' => 'Themes',
        'add_new_item' => 'Add New Theme',
        'edit_item' => 'Edit Theme',
        'all_items' => 'All Themes',
        'singular_name' => 'Theme'

      ),
      'menu_icon' => 'dashicons-buddicons-tracking'
    ));
    //Reviews Post Type
      register_post_type('review', array(
        'capability_type' => 'review',
        'supports' => array('title', '',  'thumbnail'),
        'rewrite' => array('slug' => 'review'),
        'public' => true,
        'labels' => array(
          'name' => 'Reviews',
          'add_new_item' => 'Add New Review',
          'edit_item' => 'Edit Review',
          'all_items' => 'All Reviews',
          'singular_name' => 'Review'

        ),
        'menu_icon' => 'dashicons-buddicons-tracking'
      ));


}

add_action('init', 'kikks_post_types');
