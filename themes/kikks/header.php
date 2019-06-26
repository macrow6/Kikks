<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kikks
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'kikks' ); ?></a>

	<header id="masthead" class="site-header">

		<!-- ------------------ Navigation --------------------- -->

	    <nav class="navbar navbar-expand-lg navbar-light bg-light">
	      <div class="container">
	        <a class="navbar-brand" href="<?php echo site_url('/'); ?>"><span id="brown">Kikks</span> and <span id="pink">Giggles</span></a>
	        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	          <span class="navbar-toggler-icon"></span>
	        </button>
	        <div class="collapse navbar-collapse" id="navbarSupportedContent">
	          <ul class="navbar-nav mr-auto">
	            <li class="nav-item active">
	              <a class="nav-link" href="<?php echo site_url('/'); ?>">Home <span class="sr-only">(current)</span></a>
	            </li>
	            <li class="nav-item">
	              <a class="nav-link" href="<?php echo get_post_type_archive_link('character'); ?>">Characters</a>
	            </li>
	            <li class="nav-item">
	              <a class="nav-link" href="<?php echo get_post_type_archive_link('theme'); ?>">Themes</a>
	            </li>
	            <li class="nav-item">
	              <a class="nav-link" href="#">Packages</a>
	            </li>
	            <li class="nav-item">
	              <a class="nav-link" href="#">Why Choose Us</a>
	            </li>
	          </ul>
	          <a href="tel:2812218931" class="navbar-brand">CALL NOW: 281-221-8931</a>

	          <?php

	            if(is_user_logged_in()) { ?>

	              <a href="<?php echo wp_logout_url(); ?>" class="btn btn-outline-danger">Logout</a>

	          <?php  }else { ?>

	              <a href="<?php echo wp_login_url(); ?>" class="btn btn-outline-success">Login</a>

	        <?php    }

	          ?>



	        </div>
	        </div>
	  </nav>


	</header><!-- #masthead -->

	<div id="content" class="site-content">
