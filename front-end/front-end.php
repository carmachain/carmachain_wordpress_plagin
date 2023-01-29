<?php

namespace carmachain_wordpress_plugin;

/**
 * Checks whether the purchase was just made. If so, and payment method was Carmachain,
 * then show the After Order Popup
 * @return void
 */
function displayAfterOrderPopup(): void
{
    if (! function_exists('WC')) {
        // WooCommerce not installed
        return;
    }

    if (!is_wc_endpoint_url( 'order-received' )) {
        return;
    }

    $order_id = absint( get_query_var('order-received') ); // Get order ID
    if (get_post_type($order_id) !== 'shop_order') {
        return;
    }

    $order = wc_get_order($order_id);
    $carmachainPaymentGateway = get_option('carmachain_woocommerce_payment_gateway');
    if ($order->get_payment_method() !== $carmachainPaymentGateway) {
        return;
    }

    wp_enqueue_style( 'carmachain-after-order-popup', plugin_dir_url(__FILE__) . 'after-order-popup.css');
    wp_enqueue_script( 'carmachain-after-order-popup', plugin_dir_url(__FILE__) . 'after-order-popup.js');

    $carmachainAfterOrderPopupUrl = get_option('carmachain_after_order_popup_url');
    $carmachainJsObject = [
        'afterOrderPopupUrl'       => $carmachainAfterOrderPopupUrl,
        'afterOrderPopupUrlOrigin' => getUrlOrigin($carmachainAfterOrderPopupUrl)
    ];

    wp_localize_script('carmachain-after-order-popup', 'carmachain', $carmachainJsObject);
}
add_action('wp_enqueue_scripts', '\carmachain_wordpress_plugin\displayAfterOrderPopup');


/**
 * Extract the origin from full URL
 * @param string $url
 * @return string
 */
function getUrlOrigin(string $url)
{
    $purl = parse_url($url);
    $ret = $purl['scheme'] ?? 'http';
    $ret .= '://';
    $ret .= $purl['host'];

    if (isset($purl['port'])) {
        $ret .= ":" . $purl['port'];
    }

    return $ret;
}