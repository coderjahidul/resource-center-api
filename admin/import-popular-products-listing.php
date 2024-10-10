<?php
function insert_single_product_in_db(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_popular_search_products';
    $products = $wpdb->get_results(
        "SELECT id, value FROM $table_name WHERE status = 'panding' ORDER BY id ASC LIMIT 1"
    );
    $serial_id = $products[0]->id;
    // $products = json_decode($products[0]->value, true);
    // $products_title = $products['title'];
    


    // print_r($products);

    $popular_search_product = json_decode($products[0]->value, true);
    $product_unique_id = $popular_search_product['productId'] ?: '';
    $product_title = $popular_search_product['productDetails']['title'] ?? '';
    $product_description = $popular_search_product['longDescription'] ?? '';
    // price
    $product_msrp = $popular_search_product['productPrice']['listPriceRange'] ?? 0;
    $product_sale_price = $popular_search_product['productPrice']['salePriceRange'] ?? 0;
    // $product_discount = ($product_msrp - $product_sale_price) ?? 0;
    $product_estimated_total_price = $popular_search_product['productPrice']['salePriceRange'] ?? 0;
    // Key Specs
    $product_features = $popular_search_product['productDetails']['productSpecification']['features'] ?? '';
    // get product class features
    foreach ($product_features as $feature) {
        if ($feature['featureName'] === 'Payload Capacity') {
            $product_max_payload = $feature['featureValues'][0];
        }elseif($feature['featureName'] === 'Classification') {
            $product_class = $feature['featureValues'][0];
        }elseif($feature['featureName'] === 'All Electric Range') {
            $product_range = $feature['featureValues'][0];
        }
    }
    

    $product_skus = $popular_search_product['skus'];
    foreach($product_skus as $key => $value) {
        // Product Thumbnail
        $thumbnail = $value['media']['thumbnailImg'] ?? '';
        // Product Stock
        $product_stock = $value['skuDetails']['inventory'][0]['availableStock'] ?? 0;
        // Product VIN
        $product_vin = $value['mfgPartNumber'] ?? '';
        // Product Price
        $product_price = $value['skuDetails']['price']['effectivePrice'] ?? 0;
        // description
        $product_body_description = $value['skuDetails']['description'] ?? '';

    }


    // Define product unique ID for the product
    // $product_id = $product_unique_id;

   // Check if the property with this product unique ID already exists
    $args = array(
        'post_type' => 'popular_products',
        'meta_query' => array(
            array(
                'key' => '_product_uniqueID',
                'value' => $product_unique_id,
                'compare' => '='
            ),
        ),
    );

    $existing_property = new WP_Query($args);

    if ($existing_property->have_posts()) {
        // Property exists, update it
        $existing_property->the_post();
        $product_id = get_the_ID();
    } else {
        // Property does not exist, create a new one
        $product_id = wp_insert_post(array(
            'post_title' => $product_title,
            'post_content' => $product_description,
            'post_status' => 'publish',
            'post_type' => 'popular_products',
        ));
    }

    // Update property meta
    update_post_meta($product_id, '_product_uniqueID', $product_unique_id);
    update_post_meta($product_id, 'vin_number', $product_vin);
    update_post_meta($product_id, 'product_class', $product_class);
    // update_post_meta($product_id, 'product_range', $product_range);
    update_post_meta($product_id, 'product_max_payload', $product_max_payload);

    // product stock 
    update_post_meta($product_id, 'product_stock', $product_stock);
    // update_post_meta($product_id, 'product_battery_capacity', $product_battery_capacity);
    // update_post_meta($product_id, '_product_sale_price', $product_sale_price);
    // update_post_meta($product_id, '_product_estimated_total_price', $product_estimated_total_price);
    // update_post_meta($product_id, '_product_thumbnail', $thumbnail);
    // update_post_meta($product_id, '_product_stock', $product_stock);
    // update_post_meta($product_id, '_product_vin', $product_vin);
    // update_post_meta($product_id, '_product_price', $product_price);
    // update_post_meta($product_id, '_product_body_description', $product_body_description);

    // Update the status of the processed Property in your database
    $wpdb->update(
        $table_name,
        ['status' => 'completed'],
        ['id' => $serial_id]
    );
    // Reset post data
    wp_reset_postdata();

    return "Property imported successfully.";

    
    
    
}