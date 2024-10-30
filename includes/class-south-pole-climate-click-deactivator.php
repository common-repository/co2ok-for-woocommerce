<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    south-pole-climate-click
 * @subpackage south-pole-climate-click/includes
 * @author     ClimateClick
 */
class South_Pole_Climate_Click_Deactivator
{

    /**
     * @since    1.0.0
     */
    public static function deactivate()
    {
        self::callback_deactivation();
    }

    public static function callback_deactivation()
    {
        $id = get_option('api_key_id');
        $siteUrl = get_bloginfo('url');
        $climateClickSecretKey = get_option('climate_click_secret_key');
        $apiUrl = new South_Pole_Climate_Click_API();

        if (!empty($id) && !empty($siteUrl) && !empty($climateClickSecretKey)) {
            $deactivateApiUrl = API_KEY_URL . "deactivated";
            $deactivatePayload = '{
                "source": {
                    "url": "' . $siteUrl . '",
                    "shopId": "' . $id . '"
                },
                "data": {},
                "meta": {
                    "timestamp": 1672396240
                }
            }';

            $deactivateHeaderSignature = hash_hmac('sha256', $deactivatePayload, $climateClickSecretKey);
            $deactivateHeaders = array(
                'Accept: application/json',
                'Content-Type: application/json',
                'wp-shop-signature: ' . $deactivateHeaderSignature
            );

            /* Call the Deactivate API */
            $deactivateApiResponseArray = $apiUrl->get_api_key_response(
                $deactivateApiUrl,
                $deactivateHeaders,
                'POST',
                $deactivatePayload
            );
            
            if (!empty($deactivateApiResponseArray)) {
                if ($deactivateApiResponseArray['httpcode'] != '200') {
                    echo $deactivateApiResponseArray['response']['error'];
                    die();
                }
            }
        }
    }
}
