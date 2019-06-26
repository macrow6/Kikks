<?php

function kikks_files() {
  wp_enqueue_style('googleFonts2', 'https://fonts.googleapis.com/css?family=Anton&display=swap');
  wp_enqueue_style('googleFonts1', 'https://fonts.googleapis.com/css?family=Rubik&display=swap');
  wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('bs-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');

  wp_enqueue_style('kikksMainCss', get_stylesheet_uri());
}

add_action('wp_enqueue_scripts', 'kikks_files');


function kikks_features() {
  add_theme_support('title-tag');



}

add_action('after_setup_theme', 'kikks_features');


//Redirect to front page
add_action('admin_init', 'redirectToHome');

function redirectToHome() {
$currentUser = wp_get_current_user();

if(count($currentUser->roles) == 1 AND $currentUser->roles[0] == 'phmm_client') {
  wp_redirect(site_url('/index.php/testing-page'));
  exit;
}

}


//Hide admin bar for customers
add_action('wp_loaded', 'noCustomerAdminBar');

function noCustomerAdminBar() {
$currentUser = wp_get_current_user();

if(count($currentUser->roles) == 1 AND $currentUser->roles[0] == 'phmm_client') {
  show_admin_bar(false);
}

}

//My Custom Fuctions
function myCharCount() {
$numberOfCharacters = wp_count_posts('character');
$numberOfCharacters = $numberOfCharacters->publish;
echo $numberOfCharacters - 1;
}
function myThemeCount() {
$numberOfThemes = wp_count_posts('theme');
$numberOfThemes = $numberOfThemes->publish;
echo $numberOfThemes - 1;
}




 ?>
