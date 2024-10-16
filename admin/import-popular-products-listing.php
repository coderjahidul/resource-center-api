<?php
function insert_single_product_in_db(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_popular_search_products';
    $products = $wpdb->get_results(
        "SELECT id, value FROM $table_name WHERE status = 'panding' ORDER BY id ASC LIMIT 1"
    );
    $serial_id = $products[0]->id;
    $products = json_decode($products[0]->value, true);
    $data = $products; // Define the $data variable
    

    $product_title = $data['seoData']['title'] ?? 'Not Found';
    // Separate the data into separate variables
    $product_unique_id = $data['productId'] ?? '';
    $displayName = $data['displayName'] ?? null;
    $longDisplayName = $data['longDisplayName'] ?? null;
    $description = $data['description'] ?? null;
    $longDescription = $data['longDescription'] ?? null;
    $productType = $data['productType'] ?? null;
    $pdpSeoUrl = $data['pdpSeoUrl'] ?? null;
    $onlineOnly = $data['onlineOnly'] ?? null;
    $rating = $data['rating'] ?? null;
    $modelCode = $data['modelCode'] ?? null;
    $manufacturer = $data['manufacturer'] ?? null;
    $allowInStorePickup = $data['allowInStorePickup'] ?? null;

    $skus = $data['skus'] ?? [];
    $skuSwatches = $data['skuSwatches'] ?? [];
    $variantOptions = $data['variantOptions'] ?? [];
    $brand = $data['brand'] ?? [];
    $productInventoryVo = $data['productInventoryVo'] ?? [];
    $productPrice = $data['productPrice'] ?? [];
    $defaultSkuId = $data['defaultSkuId'] ?? null;
    $hasStock = $data['hasStock'] ?? null;
    $hasPrice = $data['hasPrice'] ?? null;
    $hazmat = $data['hazmat'] ?? null;
    $restricted = $data['restricted'] ?? null;
    $additionalDetails = $data['additionalDetails'] ?? [];
    $productDetails = $data['productDetails'] ?? [];
    $associatedWarrantyIds = $data['associatedWarrantyIds'] ?? [];
    $breadcrumbs = $data['breadcrumbs'] ?? [];
    $seoData = $data['seoData'] ?? [];
    $generateSiteMapForSKU = $data['generateSiteMapForSKU'] ?? null;
    $startDate = $data['startDate'] ?? null;
    $endDate = $data['endDate'] ?? null;
    $active = $data['active'] ?? null;
    $searchable = $data['searchable'] ?? null;
    $navigable = $data['navigable'] ?? null;
    $clearance = $data['clearance'] ?? null;
    $bundle = $data['bundle'] ?? null;
    $accessory = $data['accessory'] ?? null;
    $component = $data['component'] ?? null;

    $product_skus = $data['skus'];
    foreach($product_skus as $key => $value) {
        // Product Thumbnail
        $thumbnail_img = $value['media']['largeImg'] ?? '';
        // Product Stock
        $product_stock = $value['skuDetails']['inventory'][0]['availableStock'] ?? 0;
        // Product VIN
        $product_vin = $value['mfgPartNumber'] ?? '';
        // Product Price
        $product_price = $value['skuDetails']['price']['effectivePrice'] ?? 0;
        // description
        $product_body_description = $value['skuDetails']['description'] ?? '';

    }

    // Accessing nested data
    if (isset($skus)) {
        foreach ($skus as $skuId => $skuData) {
            $skuDisplayName = $skuData['displayName'] ?? null;
            $skuLongDisplayName = $skuData['longDisplayName'] ?? null;
            $skuDescription = $skuData['description'] ?? null;
            $skuLongDescription = $skuData['longDescription'] ?? null;
            $mfgPartNumber = $skuData['mfgPartNumber'] ?? null;
            // Accessing media data
            if (isset($skuData['media'])) {
                $media = $skuData['media'];
                $smallmg = $media['smallImg'] ?? null;
                $largeImg = $media['largeImg'] ?? null;
                $thumbnailImg = $media['thumbnailImg'] ?? null;
                $swatchImg = $media['swatchImg'] ?? null;
                if (isset($media['alternateImages'])) {
                    foreach ($media['alternateImages'] as $alternateImage) {
                        $alternateImage_smallImg = $alternateImage['smallImg'] ?? null;
                        $alternateImage_largeImg = $alternateImage['largeImg'] ?? null;
                        $alternateImage_thumbnailImg = $alternateImage['thumbnailImg'] ?? null;
                        $alternateImage_swatchImg = $alternateImage['detailImg'] ?? null;
                        $alternateImage_altText = $alternateImage[' alt Text'] ?? null;
                    }
                }
            }
        }
    } else {
        echo "No SKUs found.\n";
    }

    // Accessing nested brand data
    if (isset($brand['brandId'])) {
        $brandId = $brand['brandId'] ?? null;
        $brandDisplayName = $brand['displayName'] ?? null;
        $brandSmallImg = $brand['smallImg'] ?? null;
        $brandLargeImg = $brand['largeImg'] ?? null;
    } else {
        $brandId = null;
        $brandDisplayName = null;
        $brandSmallImg = null;
        $brandLargeImg = null;
    }

    // Accessing nested productInventoryVo data
    if (isset($productInventoryVo['inventoryStatusLabel'])) {
        $inventoryStatusLabel = $productInventoryVo['inventoryStatusLabel'] ?? null;
        $alwaysInStock = $productInventoryVo['alwaysInStock'] ?? null;
        $storeOnly = $productInventoryVo['storeOnly'] ?? null;
        $stockAvailable = $productInventoryVo['stockAvailable'] ?? null;
        $backorderable = $productInventoryVo['backorderable'] ?? null;
        $preOrderable = $productInventoryVo['preOrderable'] ?? null;
    } else {
        $inventoryStatusLabel = null;
        $alwaysInStock = null;
        $storeOnly = null;
        $stockAvailable = null;
        $backorderable = null;
        $preOrderable = null;
    }
    // Accessing nested productInventoryVo data
    // if (isset($productInventoryVo['inventory'])) {
    //     $inventory = $productInventoryVo['inventory'] ?? [];
    //     foreach ($inventory as $inventoryItem) {
    //         $locationId = $inventoryItem['locationId'] ?? null;
    //         $inventoryStatusLabel = $inventoryItem['inventoryStatusLabel'] ?? null;
    //         $availableStock = $inventoryItem['availableStock'] ?? null;
    //         $backorderEnabled = $inventoryItem['backorderEnabled'] ?? null;
    //         $preOrderEnabled = $inventoryItem['preOrderEnabled'] ?? null;
    //         $alwaysInStock = $inventoryItem['alwaysInStock'] ?? null;
    //         $storePickUp = $inventoryItem['storePickUp'] ?? null;
    //     }
    // } else {
    //     $locationId = null;
    //     $inventoryStatusLabel = null;
    //     $availableStock = null;
    //     $backorderEnabled = null;
    //     $preOrderEnabled = null;
    //     $alwaysInStock = null;
    //     $storePickUp = null;
    // }

    // Accessing nested productPrice data
    if (isset($productPrice['minListPrice'])) {
        $minListPrice = $productPrice['minListPrice'] ?? null;
        $maxListPrice = $productPrice['maxListPrice'] ?? null;
        $minSalePrice = $productPrice['minSalePrice'] ?? null;
        $maxSalePrice = $productPrice['maxSalePrice'] ?? null;
        $listPriceRange = $productPrice['listPriceRange'] ?? null;
        $salePriceRange = $productPrice['salePriceRange'] ?? null;
        $onSale = $productPrice['onSale'] ?? null;
        $hasPrice = $productPrice['hasPrice'] ?? null;
        $canAllowPurchaseInqry = $productPrice['canAllowPurchaseInqry'] ?? null;
    } else {
        $minListPrice = null;
        $maxListPrice = null;
        $minSalePrice = null;
        $maxSalePrice = null;
        $listPriceRange = null;
        $salePriceRange = null;
        $onSale = null;
        $hasPrice = null;
        $canAllowPurchaseInqry = null;
    }

    // Accessing nested productDetails data
    if (isset($productDetails['productMedia'])) {
        $productMedia = $productDetails['productMedia'] ?? [];
        $productAssociations = $productDetails['productAssociations'] ?? [];
        $productSpecification = $productDetails['productSpecification'] ?? [];
        $hasProductOptions = $productDetails['hasProductOptions'] ?? null;
        $productCategory = $productDetails['productCategory'] ?? [];
        $isGiftItem = $productDetails['isGiftItem'] ?? null;
    } else {
        $productMedia = [];
        $productAssociations = [];
        $productSpecification = [];
        $hasProductOptions = null;
        $productCategory = [];
        $isGiftItem = null;
    }

    // Accessing nested productSpecification data
    $features = [];
    if (isset($productSpecification['features'])) {
        $features = $productSpecification['features'] ?? [];
    }
    // Define Title
    // $post_title = isset($product_title) ? $product_title : $displayName;
    // if(!empty($product_title) || !is_null($product_title)) {
    //     $post_title = sanitize_text_field($product_title);
    // }else{
    //     $post_title = $displayName;
    // }


    // Define Description
    $post_description = $description ?? $longDescription;
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

    $existing_product = new WP_Query($args);

    if ($existing_product->have_posts()) {
        // Property exists, update it
        $existing_product->the_post();
        

    } else {

        if($products) {
            // Property does not exist, create a new one
        $product_id = wp_insert_post(array(
            'post_title' => $product_title,
            'post_content' => $post_description,
            'post_status' => 'publish',
            'post_type' => 'popular_products',
        ));

        // Update property meta
        update_post_meta($product_id, '_product_uniqueID', $product_unique_id);
        update_post_meta($product_id, 'vin_number', $product_vin);
        update_post_meta( $product_id, 'product_stock', $product_stock );
        update_post_meta( $product_id, 'product_stock_status', $inventoryStatusLabel );
        foreach ($features as $feature) {
            $featureName = $feature['featureName'] ?? null;
            $featureValues = $feature['featureValues'][0] ?? [];
            if($featureName == 'Payload Capacity'){
                update_post_meta($product_id, 'product_max_payload', $featureValues);
            }
            if($featureName == 'Battery Capacity'){
                update_post_meta($product_id, 'product_battery_capacity', $featureValues);
            }
            if($featureName == 'Range' || $featureName == 'All Electric Range'){
                update_post_meta($product_id, 'product_range', $featureValues);
            }
            if($featureName == 'Charging'){
                update_post_meta($product_id, 'product_charge_speed', $featureValues);
            }
            if($featureName == 'Location'){
                update_post_meta($product_id, 'product_location', $featureValues);
            }
        }
        update_post_meta($product_id, 'product_msrp', $listPriceRange);
        update_post_meta($product_id, 'product_sale_price', $salePriceRange);
        $product_discount_price = $minSalePrice - $minListPrice;
        update_post_meta($product_id, 'product_discount', $product_discount_price);
        update_post_meta($product_id, 'product_estimated_total_price', $salePriceRange);

        // product body Description
        update_post_meta($product_id, 'body_description', $product_body_description);

        // $thumbnail_img = urldecode( $thumbnail_img );
        // Set featured image
        // set_featured_image_for_product($product_id, $thumbnail_img);
        set_featured_image_for_products($product_id, $thumbnail_img);
        
        // Update the status of the processed Property in your database
        $wpdb->update(
            $table_name,
            ['status' => 'completed'],
            ['id' => $serial_id]
        );

        // Reset post data
        wp_reset_postdata();

        return "Product imported successfully.";
        }

        
    }

    
}

function set_featured_image_for_products($post_id, $thumbnail_img_url) {
    // Default image URL if the specified image does not exist
    $default_img_url = 'https://example.com/path-to-default-image/default-thumbnail.jpg';
    
    // Check if the thumbnail image URL is valid and accessible
    $headers = @get_headers($thumbnail_img_url);
    
    // If the image does not exist (status code not 200), use the default image URL
    if (!$headers || strpos($headers[0], '200') === false) {
        $thumbnail_img_url = $default_img_url;
    }
    
    // Get WordPress upload directory
    $upload_dir = wp_upload_dir();
    
    // Get the image data from the provided URL (or default URL)
    $image_data = file_get_contents($thumbnail_img_url);
    
    // Extract the filename from the URL
    $filename = basename($thumbnail_img_url);
    
    // Prepare the file path where the image will be saved
    $file = $upload_dir['path'] . '/' . $filename;
    
    // Write the image data to the file
    file_put_contents($file, $image_data);
    
    // Get the file type (MIME type)
    $wp_filetype = wp_check_filetype($filename, null);
    
    // Create an attachment array
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    
    // Insert the attachment to the WordPress media library and get its ID
    $attach_id = wp_insert_attachment($attachment, $file, $post_id);
    
    // Include the image.php file required for generating attachment metadata
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    // Generate the attachment metadata (like image sizes) and update the database
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
    wp_update_attachment_metadata($attach_id, $attach_data);
    
    // Set the image as the featured image for the given post (product)
    set_post_thumbnail($post_id, $attach_id);
}
