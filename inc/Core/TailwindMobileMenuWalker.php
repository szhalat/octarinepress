<?php

namespace octarinepress\Core;

use Walker_Nav_Menu;

class TailwindMobileMenuWalker extends Walker_Nav_Menu
{
    public function start_lvl(&$output, $depth = 0, $args = [])
    {
        $indent = str_repeat("\t", $depth);

        $output .= "\n{$indent}<ul class=\"nested-submenu hidden\">\n";

    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $li_attributes = '';

        $class_names = $value = '';

        $classes = empty($item->classes) ? [] : (array) $item->classes;

        //check if item has children
        $classes[] = ($args->walker->has_children) ? 'dropdown' : '';

        //If link is active
        $classes[] = ($item->current || $item->current_item_ancestor) ? 'active' : '';

        $classes[] = 'relative z-10 mb-4 menu-item-' . $item->ID;

        $classes[] = '';

        if ($depth && $args->walker->has_children) {
            $classes[] = 'dropdown-submenu py-4 w-full font-light';
        }

        if ($depth === 0) {
            $classes[] = 'text-right';
        }

        if ($depth !== 0) {
            $classes[] = '';
        }

        //join array with classes
        $class_names = implode(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));

        //escaped attributes for wordpress
        $class_names = ' class="' . esc_attr($class_names) . '"';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
        $id = strlen($id) ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';

        //attributes
        $attributes = ! empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= ! empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= ! empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= ! empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        //data for dropdown if necessary

        //first level
        $attributes .= ($depth == 0) ? ' class="relative block p-4 tracking-widest"' : '';

        $attributes .= ($args->walker->has_children && $depth == 1) ? ' class="text-red block relative border-b border-gray pb-3"' : '';

        $attributes .= ($depth == 2) ? ' class="block hover:text-red"' : '';

        $submenu_icon = ($depth == 1) ? '<span class="icon-right-small" aria-hidden="true"></span>' : '';

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>' . $submenu_icon;
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;

        $item_output .= (($depth == 0 && $args->walker->has_children) || ($depth == 1 && $args->walker->has_children)) ? ' </a><button class="open-submenu" aria-label="otwarcie podmenu"><svg width="27" height="22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.5 8l3.75 3.75L17 8l1.5.75L13.25 14 8 8.75 9.5 8z" fill="#fff"/></svg></button>' : '</a>';

        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}
