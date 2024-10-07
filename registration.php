<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Shortcode for the custom registration form
function thinker_custom_registration_form() {
    if (is_user_logged_in()) {
        return '<p>' . __('You are already logged in.', 'thinker-domain') . '</p>';
    }

    ob_start(); // Start output buffering
    ?>

    <div class="thinker-registration-form">
        <form method="post" action="">
            <p>
                <label for="username"><?php _e('Username', 'thinker-domain'); ?></label>
                <input type="text" name="username" id="username" required />
            </p>
            <p>
                <label for="email"><?php _e('Email', 'thinker-domain'); ?></label>
                <input type="email" name="email" id="email" required />
            </p>
            <p>
                <label for="password"><?php _e('Password', 'thinker-domain'); ?></label>
                <input type="password" name="password" id="password" required />
            </p>
            <p>
                <label for="role"><?php _e('Register as', 'thinker-domain'); ?></label>
                <select name="role" id="role">
                    <option value="user"><?php _e('User', 'thinker-domain'); ?></option>
                    <option value="mentor"><?php _e('Mentor', 'thinker-domain'); ?></option>
                </select>
            </p>
            <p>
                <input type="submit" name="custom_user_register" value="<?php _e('Register', 'thinker-domain'); ?>" />
            </p>
        </form>
    </div>

    <?php
    return ob_get_clean(); // Return the buffered content
}
add_shortcode('thinker_registration_form', 'thinker_custom_registration_form');

// Handle form submission and user registration
function thinker_handle_user_registration() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['custom_user_register'])) {
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $role = sanitize_text_field($_POST['role']);
        $errors = new WP_Error();

        // Validate form input
        if (empty($username) || empty($email) || empty($password)) {
            $errors->add('field', __('Please fill in all fields.', 'thinker-domain'));
        }
        if (username_exists($username)) {
            $errors->add('user_name', __('Username already exists.', 'thinker-domain'));
        }
        if (!is_email($email)) {
            $errors->add('email_invalid', __('Invalid email address.', 'thinker-domain'));
        }
        if (email_exists($email)) {
            $errors->add('email', __('Email address already exists.', 'thinker-domain'));
        }
        if (!in_array($role, array('mentor', 'user'))) {
            $errors->add('role', __('Invalid role selection.', 'thinker-domain'));
        }

        // If no errors, create user
        if (empty($errors->get_error_messages())) {
            $user_id = wp_create_user($username, $password, $email);
            if (!is_wp_error($user_id)) {
                $user = new WP_User($user_id);
                $user->set_role($role);

                // Log the user in and redirect to homepage
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
                wp_redirect(home_url());
                exit;
            } else {
                echo '<div class="error">' . $user_id->get_error_message() . '</div>';
            }
        } else {
            foreach ($errors->get_error_messages() as $error) {
                echo '<div class="error">' . $error . '</div>';
            }
        }
    }
}
add_action('template_redirect', 'thinker_handle_user_registration');

?>
