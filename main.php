<?php
/*
Plugin Name: Mentor & User Registration
Description: A plugin developed to provide custom functionalities in thinker.co.
Version: 1.1.0
Author: Ingenieur.co.in
Author URI: https://ingenieur.co.in
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

include_once plugin_dir_path(__FILE__) . 'registration.php';
// Include custom product fields
include_once plugin_dir_path(__FILE__) . 'custom-product-fields.php';

include_once plugin_dir_path(__FILE__) . 'get-products.php';

// Create custom roles on plugin activation
function thinker_register_custom_roles() {
    add_role('mentor', __('Mentor'), array(
        'read' => true, // Allows reading posts
        'edit_posts' => false, // Disallow editing posts
    ));

    add_role('user', __('User'), array(
        'read' => true, // Allows reading posts
        'edit_posts' => false, // Disallow editing posts
    ));
}
register_activation_hook(__FILE__, 'thinker_register_custom_roles');

// Remove custom roles on plugin deactivation
function thinker_remove_custom_roles() {
    remove_role('mentor');
    remove_role('user');
}
register_deactivation_hook(__FILE__, 'thinker_remove_custom_roles');

?>