<?php
/**
 * Custom Navigation Walker
 *
 * @package Dreami
 */

class Dreami_Nav_Walker extends Walker_Nav_Menu {
    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $active_class = in_array('current-menu-item', $classes) ? 'text-white bg-blue-700 rounded-sm md:bg-transparent md:text-blue-700 md:p-0 md:dark:text-blue-500' : 'text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 md:dark:hover:text-blue-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700';
        
        $output .= '<li class="' . esc_attr(implode(' ', $classes)) . '">';
        $output .= '<a href="' . esc_url($item->url) . '" class="block py-2 px-3 ' . $active_class . '"' . ($item->target ? ' target="' . esc_attr($item->target) . '"' : '') . '>' . esc_html($item->title) . '</a>';
    }
} 