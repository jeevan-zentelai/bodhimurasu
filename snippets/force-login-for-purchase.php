<?php
function force_login_to_purchase() {
    if (!is_user_logged_in()) {
        if (is_cart() || is_checkout()) {
            wp_redirect(wc_get_page_permalink('myaccount'));
            exit;
        }
    }
}
add_action('template_redirect', 'force_login_to_purchase');