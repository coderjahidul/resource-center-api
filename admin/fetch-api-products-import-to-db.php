<?php
function popular_truncate_table( $table_name ) {
    global $wpdb;
    $wpdb->query( "TRUNCATE TABLE $table_name" );
}
function fatch_popular_search_products() {

    // Step 1: Get Authorization Token
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://cev-api.prd.ops.retisio.io/auth/api/v1/clients/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "client_id":"retisiocevclient",
            "client_secret":"7+ZF30hNjyFiSme2P3I04HIDNWiO68QLDKLK8ibFvXs=",
            "grant_type": "client_credentials"
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $auth_response = json_decode($response);
    if (!isset($auth_response->access_token)) {
        die('Error: Failed to retrieve access token');
    }

    $authorization = 'Bearer ' . $auth_response->access_token;

    // Step 2: Call API to get product IDs
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://cev-qa-api.lle.ops.retisio.io/content/api/v1/content-items/productCarousel:default:77ce304a-6952-444b-8fa8-dd6d6638ee18/live',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'x-chnl-id: CEV_CHANNEL',
            'Authorization: ' . $authorization // Use dynamic token here
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    // Decode the response
    $product_data = json_decode($response, true);

    if (!isset($product_data['configValue'])) {
        die('Error: configValue not found in the response');
    }

    // Extract product IDs from configValue
    $config_value = json_decode($product_data['configValue'], true); // Decode configValue as it's a JSON string
    if (!isset($config_value['defaultProductSelector']['products'])) {
        die('Error: Product IDs not found in configValue');
    }

    $product_ids = array_column($config_value['defaultProductSelector']['products'], 'productId');

    // Step 3: Call for popular search products API with '+' as delimiter for product IDs
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://cev-qa-api.lle.ops.retisio.io/catalog-search/api/v3/products?tinyProduct=false&productIds=' . implode('+', $product_ids), // Use '+' as delimiter
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'x-chnl-id: CEV_CHANNEL',
            'Authorization: ' . $authorization // Ensure $authorization is defined properly
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    // Step 4: Return the response of popular products
    // print_r($response);
    return $response;
}

function insert_products_import_array_in_db(){
    // Call function to get API response
    $api_response = fatch_popular_search_products();
    $popular_search_products = json_decode($api_response, true);

    // Get global $wpdb
    global $wpdb;

    // Create Sync Popular Search Products Table
    $table_name = $wpdb->prefix . 'sync_popular_search_products';

    // Truncate table
    popular_truncate_table( $table_name );

    // Loop through each property and insert it into the database
    foreach ($popular_search_products['payLoad']['products'] as $product) {
        // Check if 'unique_id' exists in the property data
        if (!isset($product['productId'])) {
            continue; // Skip if unique_id is not present
        }
        
        $product_id = $product['productId'];
        
        // Check if the unique_id already exists in the database
        $id_exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM $table_name WHERE product_id = %s",
                $product_id
            )
        );

        if ($id_exists) {
            continue; // Skip to the next property if the unique_id already exists
        }

        // Prepare data for insertion
        $data = array(
            'value'      => json_encode($product), // Serialize the value if it's an array or object
            'product_id'  => $product_id,
            'status'     => "panding",
        );

        // Define format for data types ('%s' for strings, adjust as needed)
        $format = array('%s', '%s', '%s');

        // Insert property data into the database
        $result = $wpdb->insert($table_name, $data, $format);

        // Check for errors
        if ($result === false) {
            // Return or log the actual error message
            return "Error inserting Products data into database: " . $wpdb->last_error;
        }
    }

    return "Products inserted successfully.";
}