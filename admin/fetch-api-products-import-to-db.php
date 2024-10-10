<?php
function fatch_popular_search_products() {

    // Call for authorization API
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://cev-qa-api.lle.ops.retisio.io/auth/api/v1/clients/token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
        "client_id":"retisiocevclient",
        "client_secret":"gJODkQiwXiIwyJo01SYEyuJNcPHqv4t6qZeoBtdP2Vk=",
        "grant_type": "client_credentials"
    }',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $authorization = 'Bearer ' . json_decode($response)->access_token;

    // Call for popular search products API
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://cev-qa-api.lle.ops.retisio.io/catalog-search/api/v3/products?tinyProduct=false&productIds=10207+19000+100527+100133+100000+100325',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'x-chnl-id: CEV_CHANNEL',
        'Authorization: ' . $authorization
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
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