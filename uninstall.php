<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/* Call uninstall api */
$id = get_option('api_key_id');
$siteUrl = get_bloginfo('url');
$climateClickSecretKey = get_option('climate_click_secret_key');

if (!empty($id) && !empty($siteUrl) && !empty($climateClickSecretKey)) {
    $uninstallApiUrl = 'https://climate-click.shpwr.nl/api/wp/uninstall';
    $uninstallPayload = '{
        "source": {
            "url": "' . $siteUrl . '",
            "shopId": "' . $id . '"
        },
        "data": {},
        "meta": {
            "timestamp": 1672396240
        }
    }';
    
    $uninstallHeaderSignature = hash_hmac('sha256', $uninstallPayload, $climateClickSecretKey);
    $uninstallHeaders = array(
        'Accept: application/json',
        'Content-Type: application/json',
        'wp-shop-signature: ' . $uninstallHeaderSignature
    );

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $uninstallApiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $uninstallPayload,
        CURLOPT_HTTPHEADER => $uninstallHeaders,
    ));

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if ($httpcode == '200') {
        /* remove options */
        delete_option('climate_click_secret_key');
        delete_option('climate_click_confirmation_url');
        delete_option('climate_click_api_key');
    }
}
