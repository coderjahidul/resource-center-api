<?php
// Call API function 
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
// popular_search_products
add_shortcode( 'popular_search_products', 'popular_search_products_function' );

function popular_search_products_function() {

    $response = fatch_popular_search_products(); // Fetch the JSON response
    $popular_search_products = json_decode($response, true); // Decode as associative array

    ?>
    <div class="container-fluid mt-5">
        <h2 class="mb-4">Popular Searches</h2>
        <div class="row">

            <?php
            foreach ($popular_search_products['payLoad']['products'] as $popular_search_product) {
            $product_id = $popular_search_product['productId'];
            // Product Title
            $product_title = $popular_search_product['displayName'];
            // Product Features
            $product_features_name = $popular_search_product['productDetails']['productSpecification']['features'][16]['featureName'];
            $product_features_values = $popular_search_product['productDetails']['productSpecification']['features'][16]['featureValues'][0];

            $product_features_name1 = $popular_search_product['productDetails']['productSpecification']['features'][4]['featureName'];
            $product_features_values1 = $popular_search_product['productDetails']['productSpecification']['features'][4]['featureValues'][0];

            $product_features_name2 = $popular_search_product['productDetails']['productSpecification']['features'][5]['featureName'];
            $product_features_values2 = $popular_search_product['productDetails']['productSpecification']['features'][5]['featureValues'][0];

            $product_features_name3 = $popular_search_product['productDetails']['productSpecification']['features'][6]['featureName'];
            $product_features_values3 = $popular_search_product['productDetails']['productSpecification']['features'][6]['featureValues'][0];

            $product_features_name4 = $popular_search_product['productDetails']['productSpecification']['features'][7]['featureName'];
            $product_features_values4 = $popular_search_product['productDetails']['productSpecification']['features'][7]['featureValues'][0];
            
            $product_skus = $popular_search_product['skus'];
            foreach($product_skus as $key => $value) {
                // Product Thumbnail
                $thumbnail = $value['media']['thumbnailImg'];
                // Product Stock
                $product_stock = $value['skuDetails']['inventory'][0]['availableStock'] ?: 0;
                // Product VIN
                $product_vin = $value['mfgPartNumber'] ?: 'Not Available';
                // Product Price
                $product_price = $value['skuDetails']['price']['effectivePrice'] ?: 0;

            }


            ?>
            <!-- Card 1 -->
                <div class="col-lg-4 col-md-6 col-sm-12 vehicle-card">
                    <div class="card h-100 position-relative">
                        <div class="in-stock" style="z-index: 1;"><?php echo $product_stock; ?> In Stock</div>
                        <div class="image-container">
                            <img src="<?php echo $thumbnail; ?>" class="card-img-top" alt="<?php echo $product_title; ?>">
                            <div class="image-overlay"></div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title" style="font-weight: 600;"><?php echo $product_title; ?></h5>
                            <p>VIN: <?php echo $product_vin;?></p>
                            <p><strong><?php echo $product_features_name;?>:</strong> <?php echo $product_features_values;?></p>
                            <p><strong><?php echo $product_features_name1;?>:</strong> <?php echo $product_features_values1; ?>
                            </p>
                            <p><strong><?php echo $product_features_name2;?>:</strong> <?php echo $product_features_values2; ?>
                            </p>
                            <p><strong><?php echo $product_features_name3;?>:</strong> <?php echo $product_features_values3; ?>
                            </p>
                            <p><strong><?php echo $product_features_name4;?>:</strong> <?php echo $product_features_values4; ?>
                            </p>
                            <div class="price">Starting at <?php echo $product_price; ?></div>
                            <div class="button-section d-flex justify-content-between">
                                <a href="#" class="btn card-button-one" style="border: 1px solid #2FC774; background: #2FC774; color: #ffffff; font-weight: 500;">Request Info</a>
                                <a href="#" class="btn card-button-two" style="border: 1px solid #2FC774; color: #2FC774; font-weight: 500;">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
            }
            ?>
        </div>
        
    </div><?php
}