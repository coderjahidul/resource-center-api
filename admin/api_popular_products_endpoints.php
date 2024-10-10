<?php 
// api_popular_products_endpoints.php
add_action('rest_api_init', 'popular_products_api_endpoints');

function popular_products_api_endpoints() {
    register_rest_route( 'proapi/v3', '/products', [
        'methods' => 'GET',
        'callback' => 'popular_get_products_import_array'
    ]);

    register_rest_route( 'proapi/v3', '/sync_single_popular_products', [
        'methods' => 'GET',
        'callback' => 'popular_get_single_property'
    ]);
}

function popular_get_products_import_array() {
    return insert_products_import_array_in_db();
}

function popular_get_single_property() {
    return insert_single_product_in_db();
}