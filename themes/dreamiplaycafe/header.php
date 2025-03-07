<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Dreami
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<!-- Favicon -->
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/assets/favicon.ico" type="image/x-icon">
	<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=campaign" />

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">

		<!-- announcement bar -->
		<?php
		$background_colour = get_field('background_colour', 'option') ?: '#3B82F6'; // Default to blue-500 if not set
		$text_colour = get_field('text_colour', 'option') ?: '#FFFFFF'; // Default to white if not set
		$announcements = get_field('announcements', 'option');


		if ($announcements): ?>
			<div id="announcement-bar" class="relative flex overflow-hidden p-5 text-center" style="background-color: <?php echo esc_attr($background_colour); ?>; color: <?php echo esc_attr($text_colour); ?>;">
				<div class="announcement-wrapper w-full flex justify-center">
					<div class="announcement-container flex whitespace-nowrap">
						<?php foreach ($announcements as $announcement): ?>
							<div class="announcement-message mr-8 md:text-s">
								<?php echo wp_kses_post($announcement['banner_message']); ?>
							</div>
						<?php endforeach; ?>
						<?php foreach ($announcements as $announcement): ?>
							<div class="announcement-message mr-8 md:text-s">
								<?php echo wp_kses_post($announcement['banner_message']); ?>
							</div>
						<?php endforeach; ?>
						<?php foreach ($announcements as $announcement): ?>
							<div class="announcement-message mr-8 md:text-s">
								<?php echo wp_kses_post($announcement['banner_message']); ?>
							</div>
						<?php endforeach; ?>
						<?php foreach ($announcements as $announcement): ?>
							<div class="announcement-message mr-8 md:text-s">
								<?php echo wp_kses_post($announcement['banner_message']); ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<h1>Riad is here</h1>

		<header id="masthead" class="site-header">
			<div class="site-branding">
				<?php
				the_custom_logo();
				if (is_front_page() && is_home()) :
				?>
					<h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
				<?php
				else :
				?>
					<p class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></p>
				<?php
				endif;
				$dreami_description = get_bloginfo('description', 'display');
				if ($dreami_description || is_customize_preview()) :
				?>
					<p class="site-description"><?php echo $dreami_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
												?></p>
				<?php endif; ?>
			</div><!-- .site-branding -->

			<nav id="site-navigation" class="main-navigation">
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e('Primary Menu', 'dreami'); ?></button>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu-1',
						'menu_id'        => 'primary-menu',
					)
				);
				?>
			</nav><!-- #site-navigation -->
		</header><!-- #masthead -->