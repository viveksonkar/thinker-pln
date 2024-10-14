<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Create a shortcode to display the list of products assigned to the user
function display_assigned_products_to_user( $atts ) {
    // Get the current user ID or the specified user ID from shortcode attributes
    $atts = shortcode_atts( array(
        'user_id' => get_current_user_id() // Default to current logged-in user
    ), $atts );

    $user_id = $atts['user_id'];

    if ( ! $user_id ) {
        return 'No user is logged in or specified.';
    }

    // Query products that have the specified user ID in the custom field '_assigned_user_id'
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1, // Get all products
        'meta_query' => array(
            array(
                'key'     => '_assigned_user_id',
                'value'   => $user_id,
                'compare' => '='
            ),
        ),
    );

    $query = new WP_Query( $args );

    // Check if there are any products assigned to this user
    if ( $query->have_posts() ) {
        $output = '<div class="product-card-container">'; // Container for all product cards
        
        while ( $query->have_posts() ) {
            $query->the_post();
            $product = wc_get_product( get_the_ID() );
        
            // Get product data
            $product_title = $product->get_title();
            $product_price = $product->get_price();
            $product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );
        
            if ( $product_image ) {
                $product_image_url = esc_url( $product_image[0] );
            } else {
                // Default image or placeholder if no product image is available
                $product_image_url = esc_url( 'path/to/default-image.jpg' );
            }
        
            $product_short_description = $product->get_short_description();
            $product_description = $product->get_description();
        
            // Start building the HTML for each product card
            $output .= '
                <div class="product-card">
                    <div class="product-card-inner">
                        <div class="product-card-header">
                            <h2 class="product-title">' . esc_html( $product_title ) . '</h2>
                            <p class="product-short-description">' . esc_html( $product_short_description ) . '</p>
                            <div class="spacer"></div>
                            <div class="dflex">
                                <div class="product-price">$' . esc_html($product_price) . '</div>
                                <div>&bull;</div>
                                <div class="product-length">2 hours and 32 minutes</div>
                            </div>
                            <button class="view-more-button" onclick="toggleCard(this)">
                                See more offers
                                <span class="elementor-button-icon">
				                    <i aria-hidden="true" class="fas fa-angle-down"></i>
                                </span>
                            </button>
                        </div>
                        <div class="product-card-body">
                            <div class="spacer top-line"></div>
                            <img class="product-image" src="' . $product_image_url . '" alt="' . esc_attr( $product_title ) . '">
                            <div class="product-description">'. $product_description .'</div>
                            <div class="product-card-footer">
                                <a href="' . esc_url( wc_get_checkout_url() . '?add-to-cart=' . $product->get_id() ) . '" class="product-book-button">Book Now</a>
                            </div>
                            <div class="spacer bottom-line"></div>
                        </div>
                    </div>
                </div>';
        }        
        
        $output .= '</div>';
        wp_reset_postdata();
    } else {
        $output = '<p>No products assigned to this user.</p>';
    }

    return $output;
}
add_shortcode( 'assigned_products', 'display_assigned_products_to_user' );

// Add some basic styles for the product card
function add_custom_product_card_styles() {
    echo '
    <style>
    .dflex {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .spacer { padding: 6px; }
    .top-line{
        border-top: solid 1px #999999;
    }
    .bottom-line{
        border-bottom: solid 1px #999999;
    }
    .product-card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        width: 296px;
        margin: 0 0 0 -10px;

    }
    .product-card {
        border-radius: 8px;
        padding: 12px;
        width: 300px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        background: var(--pmpro--color--white);
    }
    .product-card-inner {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .product-card-header {
        margin-bottom: 10px;
    }
    .product-title {
        font-size: 18px;
        font-weight: 500;
        line-height: 28px;
        color: #000;
    }
    .product-short-description {
        color: var(--e-global-color-ffd93f);
        font-family: var(--e-global-typography-accent-font-family), Sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: 16px;
    }
    .product-description {
        font-size: 1em;
        color: #666;
        margin-top: 10px;
    }
    .product-description ul { 
        padding-left: 20px;
    }
    .product-image {
        max-width: 100%;
        height: auto;
        margin-bottom: 10px;
    }
    .product-price {
        color: var(--e-global-color-accent);
        font-family: var(--e-global-typography-accent-font-family), Sans-serif;
        font-size: 20px;
        font-weight: 500;
        line-height: 0px;
    }
    .product-length {
        color: #000000;
        font-size: 16px;
        font-weight: 400;
        line-height: 0px;
        font-family: var(--e-global-typography-accent-font-family), Sans-serif;
        font-weight: var(--e-global-typography-accent-font-weight);
    }
    .product-book-button {
        background-color: var(--e-global-color-accent);
        color: #fff;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        display: inline-block;
        margin-top: 10px;
        width: 100%;
        padding: 12px;
        text-align: center;
    }
    .product-book-button:hover { 
        color: #fff;
    } 
    .view-more-button {
        border: 1px solid #A0A0A0;
        color: #A0A0A0;
        padding: 10px;
        cursor: pointer;
        margin-top: 10px;
        border-radius: 4px;
        width: 100%;
        outline: none !important;
        background: var(--pmpro--color--white); 
    }
    .view-more-button:focus {
        outline: none; /* Ensure no outline on focus */
        box-shadow: none; /* Remove any shadow effect if applied */
    }
    /* Style for sliding transition */
    .product-card-body {
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.5s ease-in-out;
    }
    .expanded .product-card-body {
        max-height: 1000px; /* Set to a high value to accommodate expanding content */
    }
    </style>';
}
add_action('wp_head', 'add_custom_product_card_styles');

// Add JavaScript for expanding the product card with sliding transition
function add_custom_product_card_scripts() {
    echo '
    <script>
    function toggleCard(button) {
        var cardInner = button.closest(".product-card-inner");
        var cardBody = cardInner.querySelector(".product-card-body");
        
        if (cardInner.classList.contains("expanded")) {
            cardInner.classList.remove("expanded");
            //button.innerText = "View More";
        } else {
            cardInner.classList.add("expanded");
            //button.innerText = "View Less";
            button.style.display = "none";
        }
    }
    </script>';
}
add_action('wp_footer', 'add_custom_product_card_scripts');
