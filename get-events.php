<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

add_action('show_user_profile', 'add_organization_user_profile_field');
add_action('edit_user_profile', 'add_organization_user_profile_field');

function add_organization_user_profile_field($user) {
    ?>
    <h3><?php _e('Additional Information', 'textdomain'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="organization"><?php _e('Organization', 'textdomain'); ?></label></th>
            <td>
                <input type="text" name="organization" id="organization" value="<?php echo esc_attr(get_the_author_meta('organization', $user->ID)); ?>" class="regular-text" /><br />
                <span class="description"><?php _e('Please enter the name of the organization.', 'textdomain'); ?></span>
            </td>
        </tr>
    </table>
    <?php
}


add_action('personal_options_update', 'save_organization_user_profile_field');
add_action('edit_user_profile_update', 'save_organization_user_profile_field');

function save_organization_user_profile_field($user_id) {
    // Check if the current user has permission to edit the user profile.
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // Update the "Organization" field value in the database.
    update_user_meta($user_id, 'organization', sanitize_text_field($_POST['organization']));
}


// Short code for Event
function custom_event_list_shortcode($atts) {
    
    // First, check if 'user_id' is passed in the URL query string
    if ( isset( $_GET['user_id'] ) ) {
        $user_id = intval( $_GET['user_id'] ); // Sanitize the user_id from URL
    } else {
        // If no user_id is found in the URL, fall back to the shortcode attributes
        $atts = shortcode_atts( array(
            'user_id' => get_current_user_id() // Default to the currently logged-in user
        ), $atts );
        $user_id = $atts['user_id'];
    }

    // Retrieve the organization from user meta
    $org = get_user_meta($user_id, 'organization', true);

    // Extract any attributes passed to the custom shortcode (like style, columns, etc.)
    $atts = shortcode_atts(
        array(
            'style' => 'grid',  // default value for 'style'
            'column' => '1',    // default value for 'column'
        ),
        $atts
    );

    // Check if the organization is available
    if (!$org) {
        return '<p>No events found for the user.</p>'; // Fallback message
    }

    // Build the event-list shortcode with the dynamic organization
    $shortcode = '[event-list style=' . esc_attr($atts['style']) . ' column=' . esc_attr($atts['column']) . ' org=' . esc_attr($org) . ']';

    // Render the event-list shortcode
    return do_shortcode($shortcode);
}

// Register the new shortcode
add_shortcode('assigned-events', 'custom_event_list_shortcode');

