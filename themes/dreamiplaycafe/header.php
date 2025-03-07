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

	<!-- announcement bar -->
	<?php
	$background_colour = get_field('background_colour', 'option') ?: '#3B82F6'; // Default to blue-500 if not set
	$text_colour = get_field('text_colour', 'option') ?: '#FFFFFF'; // Default to white if not set
	$announcements = get_field('announcements', 'option');


	if ($announcements): ?>
		<div id="announcement-bar" class="top-0 z-999 fixed flex overflow-hidden p-5 text-center" style="background-color: <?php echo esc_attr($background_colour); ?>; color: <?php echo esc_attr($text_colour); ?>;">
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

				</div>
			</div>
		</div>
	<?php endif; ?>

	<div id="page" class="site">

		<header id="masthead" class="site-header">
			<nav class="bg-white dark:bg-gray-900 w-full z-999 top-0 fixed mt-16 z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600">
				<div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
					<a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center space-x-3 rtl:space-x-reverse">
						<?php
						if (has_custom_logo()) {
							the_custom_logo();
						} else {
							echo '<span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">' . get_bloginfo('name') . '</span>';
						}
						?>
					</a>
					<div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
						<button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Get started</button>
						<button data-collapse-toggle="navbar-sticky" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-sticky" aria-expanded="false">
							<span class="sr-only">Open main menu</span>
							<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15" />
							</svg>
						</button>
					</div>
					<div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-1',
								'menu_id'        => 'primary-menu',
								'container'      => false,
								'menu_class'     => 'flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700',
								'fallback_cb'    => false,
								'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
								'walker'         => new Dreami_Nav_Walker()
							)
						);
						?>
					</div>
				</div>
			</nav>

		</header><!-- #masthead -->