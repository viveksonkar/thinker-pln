<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Add custom field to product general tab
function thinker_add_custom_order_type_field() {
    global $woocommerce, $post;

    echo '<div class="options_group">';

    woocommerce_wp_select( array(
        'id'      => '_order_type',
        'label'   => __('Order Type', 'thinker-product-type'),
        'options' => array(
            'consulting' => __('Consulting', 'thinker-product-type'),
            'event'      => __('Event', 'thinker-product-type'),
        ),
        'desc_tip' => true,
        'description' => __('Select the order type for this product.', 'thinker-product-type'),
    ));

    echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'thinker_add_custom_order_type_field');

// Save the custom field value
function thinker_save_custom_order_type_field($post_id) {
    $order_type = isset($_POST['_order_type']) ? sanitize_text_field($_POST['_order_type']) : '';
    update_post_meta($post_id, '_order_type', $order_type);
}
add_action('woocommerce_process_product_meta', 'thinker_save_custom_order_type_field');

// Display custom field on the front end
function thinker_display_order_type_on_product_page() {
    global $post;
    $order_type = get_post_meta($post->ID, '_order_type', true);

    if ($order_type) {
        echo '<p><strong>' . __('Order Type', 'thinker-product-type') . ':</strong> ' . ucfirst($order_type) . '</p>';
    }
}
add_action('woocommerce_single_product_summary', 'thinker_display_order_type_on_product_page', 25);



// Add the user select dropdown field to the product admin page
add_action( 'woocommerce_product_options_general_product_data', 'add_user_dropdown_to_product' );

function add_user_dropdown_to_product() {
    global $post;

    // Get all users
    $users = get_users( array( 'fields' => array( 'ID', 'user_login' ) ) );

    // Get the current selected user
    $assigned_user = get_post_meta( $post->ID, '_assigned_user_id', true );

    echo '<div class="options_group">';
    woocommerce_wp_select( array(
        'id'          => '_assigned_user_id',
        'label'       => __( 'Assign User', 'woocommerce' ),
        'description' => __( 'Select a user to assign this product to.', 'woocommerce' ),
        'value'       => $assigned_user,
        'options'     => wp_list_pluck( $users, 'user_login', 'ID' ),
    ) );
    echo '</div>';
}


// Save the custom field value when the product is saved
add_action( 'woocommerce_process_product_meta', 'save_assigned_user_to_product' );

function save_assigned_user_to_product( $post_id ) {
    // Get the value from the field and save it in post meta
    $assigned_user_id = isset( $_POST['_assigned_user_id'] ) ? sanitize_text_field( $_POST['_assigned_user_id'] ) : '';
    
    update_post_meta( $post_id, '_assigned_user_id', $assigned_user_id );
}

?>
