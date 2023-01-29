<?php

namespace carmachain_wordpress_plugin;

add_action('admin_menu', function() {
    add_options_page( 'Carmachain Wordpress Plugin', 'Carmachain', 'manage_options', getPluginSlug(), '\carmachain_wordpress_plugin\showBackofficeConfiguration');
    add_action('admin_init', '\carmachain_wordpress_plugin\registerSettings' );
});

/**
 * Display the plugin settings page
 *
 * @return void
 */
function showBackofficeConfiguration(): void
{
    ?>
    <div class="wrap">
        <form method="post" action="options.php">
            <?php

            if (!function_exists('WC')) {
                echo "<h3>Error: WooCommerce not installed</h3>";
                return;
            }

            settings_fields("carmachain_settings_section");
            do_settings_sections("carmachain_options_page");
            submit_button();

            ?>
        </form>
    </div>
    <?php
}

/**
 * Register the plugin settings in WordPress core
 *
 * @return void
 */
function registerSettings(): void
{
    //section name, display name, callback to print description of section, page to which section is attached.
    add_settings_section("carmachain_settings_section", "Carmachain Wordpress Plugin", '\carmachain_wordpress_plugin\echoSettingsSectionTitle', "carmachain_options_page");

    //setting name, display name, callback to print form element, page in which field is displayed, section to which it belongs.
    add_settings_field("carmachain_woocommerce_payment_gateway", "WooCommerce payment gateway", "\carmachain_wordpress_plugin\displayPaymentGateway", "carmachain_options_page", "carmachain_settings_section");
    add_settings_field("carmachain_after_order_popup_url", "Popup Url", "\carmachain_wordpress_plugin\displayAfterOrderPopupUrl", "carmachain_options_page", "carmachain_settings_section");

    //section name, form element name, callback for sanitization
    register_setting("carmachain_settings_section", "carmachain_woocommerce_payment_gateway");
    register_setting("carmachain_settings_section", "carmachain_after_order_popup_url");
}

function echoSettingsSectionTitle(): void
{
    echo "Settings";
}

/**
 * Display Select/Option box with available WooCommerce payment methods
 *
 * @return void
 */
function displayPaymentGateway(): void
{
    $carmachainPaymentGateway = get_option('carmachain_woocommerce_payment_gateway');
    $gateways = WC()->payment_gateways->get_available_payment_gateways();
    if (!is_array($gateways)  ||  !count($gateways)) {
        echo "No WooCommerce payment methods found";
        return;
    }

    echo "<select class='regular-text' name='carmachain_woocommerce_payment_gateway' id='carmachain_woocommerce_payment_gateway'>\n";
    foreach ($gateways as $gateway) {
        echo "<option " . selected($carmachainPaymentGateway, $gateway->id) . " value='" . $gateway->id . "'>$gateway->method_title</option>\n";
    }
    echo "</select>\n";
}

/**
 * Display input with customizable Popup Url
 *
 * @return void
 */
function displayAfterOrderPopupUrl(): void
{
    $carmachainAfterOrderPopupUrl = get_option('carmachain_after_order_popup_url', 'https://app.carma.network/loan/dreamworks/k1mGZ9qqCuYfDgg3qaKCQGQkyPiL2Lf07NUB7duyYkw/e7VDatbXZqMfZXPbU6dA');

    echo "<input type='text' class='large-text' id='carmachain_after_order_popup_url' name='carmachain_after_order_popup_url' value='$carmachainAfterOrderPopupUrl'/>\n";
}

/**
 * The filter for WordPress plugin_action_links hook. Check the WordPress documentation for
 * arguments description. The filter adds "Settings" link to WordPress backoffice Plugins page
 *
 * @param array $actions
 * @param string $pluginFile
 * @param $pluginData
 * @param $context
 * @return array
 */
function pluginActionLinks(array $actions, string $pluginFile, $pluginData, $context): array
{
    if (strpos($pluginFile, getPluginSlug()) !== false) {
        $actions['settings'] = '<a href="' . esc_url( admin_url( "options-general.php?page=" . getPluginSlug() )) . '">' . __( 'Settings', 'textdomain' ) . '</a>';
    }

    return $actions;
}
add_filter('plugin_action_links', '\carmachain_wordpress_plugin\pluginActionLinks', 10, 4);

/**
 * Returns the slug of this plugin
 *
 * @return string
 */
function getPluginSlug(): string
{
    return basename( plugin_dir_path(__FILE__) );
}