<?php

namespace octarinepress\Core;

use Walker_Nav_Menu;

class TailwindMenuWalker extends Walker_Nav_Menu
{
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {

        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $li_attributes = '';

        $class_names = $value = '';

        $classes = empty($item->classes) ? [] : (array) $item->classes;

        //check if item has children
        $classes[] = ($args->walker->has_children) ? 'dropdown relative' : '';

        //If link is active
        $classes[] = ($item->current || $item->current_item_ancestor) ? 'active' : '';

        $classes[] = 'menu-item-' . $item->ID;

        $classes[] = '';

        if ($depth) {
            $classes[] = 'w-full';
        }

        if ($depth && $args->walker->has_children) {
            $classes[] = 'dropdown-submenu';
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
        $attributes .= ! empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : ' href="javascript:void(0)"';

        //data for dropdown if necessary

        $attributes .= ($depth == 0) ? ' class="relative block p-2.5"' : '';

        $attributes .= ($args->walker->has_children && $depth == 1) ? ' class="block"' : '';

        $attributes .= ($depth == 2) ? ' class="block"' : '';

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;

        $item_output .= '</a>';

        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);

    }
}
