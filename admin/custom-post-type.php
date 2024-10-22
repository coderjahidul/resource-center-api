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


// Hook to admin_menu to add the submenu page
add_action('admin_menu', 'resource_api_settings_submenu');

function resource_api_settings_submenu() {
    // Add submenu under the "Settings" menu
    add_submenu_page(
        'options-general.php',
        'Resource API Settings',
        'Resource API',
        'manage_options',
        'resource-api-settings',
        'resource_api_settings_page'
    );
}

// Callback function to display the content of the settings page
function resource_api_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Resource API Settings', 'textdomain'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('resource_api_settings_group');
            do_settings_sections('resource-api-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Hook to admin_init to register the settings
add_action('admin_init', 'resource_api_settings_init');

function resource_api_settings_init() {
    // Register new settings for the custom settings page
    register_setting('resource_api_settings_group', 'resource_api_key');
    register_setting('resource_api_settings_group', 'resource_api_url');
    register_setting('resource_api_settings_group', 'resource_api_client_id');

    // Add a new section to the custom settings page
    add_settings_section(
        'resource_api_settings_section',
        'Resource API Integration',
        'resource_api_settings_section_callback',
        'resource-api-settings'
    );

    
    add_settings_field(
        'resource_api_url',
        'API URL',
        'resource_api_url_field_callback',
        'resource-api-settings',
        'resource_api_settings_section'
    );

    add_settings_field(
        'resource_api_client_id',
        'API Client ID',
        'resource_api_client_id_field_callback',
        'resource-api-settings',
        'resource_api_settings_section'
    );

    // Add fields to the custom settings section
    add_settings_field(
        'resource_api_key',
        'Client Secret',
        'resource_api_setting_field_callback',
        'resource-api-settings',
        'resource_api_settings_section'
    );

}

// Callback for the section description (optional)
function resource_api_settings_section_callback() {
    echo '<p>' . esc_html__('Enter your Resource API credentials:', 'textdomain') . '</p>';
}

// Callback for the API key setting field
function resource_api_setting_field_callback() {
    $option = get_option('resource_api_key');
    ?>
    <input type="password" placeholder="Enter your Client Secret" name="resource_api_key" value="<?php echo esc_attr($option); ?>" />
    <?php
}

// Callback for the API URL setting field
function resource_api_url_field_callback() {
    $option = get_option('resource_api_url');
    ?>
    <input type="url" placeholder="Enter your API URL" name="resource_api_url" value="<?php echo esc_attr($option); ?>" />
    <?php
}

// Callback for the API Client ID setting field
function resource_api_client_id_field_callback() {
    $option = get_option('resource_api_client_id');
    ?>
    <input type="text" placeholder="Enter your API Client ID" name="resource_api_client_id" value="<?php echo esc_attr($option); ?>" />
    <?php
}

