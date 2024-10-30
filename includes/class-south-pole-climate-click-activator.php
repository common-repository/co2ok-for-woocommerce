<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    south-pole-climate-click
 * @subpackage south-pole-climate-click/includes
 * @author     ClimateClick
 */
class South_Pole_Climate_Click_Activator
{

    /**
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        /* Check if WooCommerce is activate or not */
        if (is_plugin_active('woocommerce/woocommerce.php')) {
            add_option('redirect_after_activation_option', true);
            self::callback_activation();
        } else {
            echo esc_html__(
                'Your plugin requires WooCommerce to be installed and activated. Please install and activate WooCommerce to use this plugin.',
                'south-pole-climate-click'
            );
            die();
        }
    }

    public static function callback_activation()
    {
        $apiUrl = new South_Pole_Climate_Click_API();
        $id = get_option('api_key_id');
        $time = time();

        if (empty($id)) {
            $generateId = "shop-" . $time;
            update_option('api_key_id', $generateId);
            $id = get_option('api_key_id');
        }

        /* Get site details */
        $siteName = get_bloginfo('name');
        $siteEmail = get_bloginfo('admin_email');
        $siteUrl = get_bloginfo('url');
        $climateClickSecretKey = get_option('climate_click_secret_key');

        /* Register API */
        if (empty($climateClickSecretKey)) {
            $registerUrl = API_KEY_URL . "register?shop-id=" . $id . "&shop-url=" . $siteUrl;
            /* Call the registration API */
            $registerApiResponse = $apiUrl->api_response($registerUrl, array(), 'GET', '');

            if ($registerApiResponse) {
                $resultArray = json_decode($registerApiResponse, true);

                if (isset($resultArray['secret'])) {
                    update_option('climate_click_secret_key', $resultArray['secret']);
                    update_option('climate_click_confirmation_url', $resultArray['confirmation_url']);
                } else {
                    if (isset($resultArray['error'])) {
                        echo $resultArray['error'];
                        die();
                    }
                }
            }
        }

        /* Confirm the registration API */
        $climateClickSecretKey = get_option('climate_click_secret_key');

        if (!empty($climateClickSecretKey)) {
            $confirmUrl = get_option('climate_click_confirmation_url');
            $confirmPayload = '{
                        "source": {
                            "url": "' . $siteUrl . '",
                            "shopId": "' . $id . '"
                        },
                        "data": {
                            "apiKey": "my-wordpress-api-key",
                            "apiSecret": "my-wordpress-api-secret"
                        },
                        "meta": {
                            "timestamp": 1672396240
                        }
                    }';
            $confirmHeaderSignature = hash_hmac('sha256', $confirmPayload, $climateClickSecretKey);

            $confirmHeaders = array(
                'Accept: application/json',
                'Content-Type: application/json',
                'wp-shop-signature: ' . $confirmHeaderSignature
            );

            /* Call the confirm registration API */
            $confirmApiResponse = $apiUrl->get_api_key_response($confirmUrl, $confirmHeaders, 'POST', $confirmPayload);

            /* Activate plugin API and generate API key */
            if ($confirmApiResponse['httpcode'] == '204') {
                $activateApiUrl = API_KEY_URL . "activated";
                $activatePayload = '{
                                "source": {
                                    "url": "' . $siteUrl . '",
                                    "shopId": "' . $id . '"
                                },
                                "data": {
                                    "email": "' . $siteEmail . '",
                                    "shopName": "' . $siteName . '"
                                },
                                "meta": {
                                    "timestamp": 1672396240
                                }
                            }';
                $activateHeaderSignature = hash_hmac('sha256', $activatePayload, $climateClickSecretKey);

                $activateHeaders = array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'wp-shop-signature: ' . $activateHeaderSignature
                );

                /* Call the activate API */
                $activateApiResponseArray = $apiUrl->get_api_key_response(
                    $activateApiUrl,
                    $activateHeaders,
                    'POST',
                    $activatePayload
                );

                if (!empty($activateApiResponseArray)) {
                    $activateApiResponse = $activateApiResponseArray['response'];
                    if (!empty($activateApiResponse)) {
                        $resultArray = json_decode($activateApiResponse, true);
                        if (isset($resultArray['apiKey'])) {
                            update_option('climate_click_api_key', $resultArray['apiKey']);
                        }
                    }
                }
            }
        }
    }
}
