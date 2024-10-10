<?php
// Register Custom Post Type Popular Search Products
function create_popular_product_post_type() {
    register_post_type('popular_products',
        array(
            'labels' => array(
                'name' => __('Popular Products'),
                'singular_name' => __('Popular Product'),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-car',
            'supports' => array('title', 'editor', 'thumbnail'),
        )
    );
}
add_action('init', 'create_popular_product_post_type');