<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    south-pole-climate-click
 * @subpackage south-pole-climate-click/includes
 * @author     ClimateClick
 */
class South_Pole_Climate_Click_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * The API class of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $api_curl The API class of this plugin.
     */
    private $api_curl;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->api_curl = new South_Pole_Climate_Click_API();
    }

    /**
     * Register the css for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/south-pole-climate-click-public.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Display climate click surcharge field on cart page.
     *
     * @since    1.0.0
     */
    public function climate_click_block_cart_page()
    {
        $pageName = "climate_click_surcharge_cart";
        $showAtFront = get_option('wc_climate_click_settings_tabs_checkbox');
        $checkoutPage = get_option('wc_climate_click_settings_tabs_page_selection');

        if ($showAtFront != "no") {
            if ($checkoutPage == 'cartPage' || $checkoutPage == 'checkoutCartPage') {
                $this->get_climate_click_surcharge_block($pageName);
            }
        }
    }

    /**
     * Display climate click surcharge field on checkout page.
     *
     * @since    1.0.0
     */
    public function climate_click_block_checkout_page()
    {
        $pageName = 'climate_click_surcharge_checkbox';
        $showAtFront = get_option('wc_climate_click_settings_tabs_checkbox');
        $checkoutPage = get_option('wc_climate_click_settings_tabs_page_selection');

        if ($showAtFront != "no") {
            if ($checkoutPage == 'checkoutPage' || $checkoutPage == 'checkoutCartPage') {
                $this->get_climate_click_surcharge_block($pageName);
            }
        }
    }

    /**
     * Climate Click surcharge block.
     *
     * @since    1.0.0
     */
    public function get_climate_click_surcharge_block($pageName)
    {
        $surcharge = $this->get_surcharge_value();
        $climateClickCheckbox = WC()->session->get('climateClickSurcharge');
        $currency = get_woocommerce_currency_symbol();
        $checked = "";
        $pluginDirectoryUrl = plugin_dir_url(dirname(__FILE__));

        if (!empty($climateClickCheckbox)) {
            $checked = "checked";
        }
        $isDisabled = ($surcharge == 0) ? 'disabled' : '';
        $climateClickSurchargeBlock = '<div class="climate-click-button">
            <div class="climate-click-button__checkbox">
                <input
                id="climate-click-surcharge-checkbox"
                type="checkbox"
                value="1"
                class="climate-click-button__checkbox-input"
                name="' . $pageName . '" ' . $checked . '
                ' . $isDisabled . '
                />
            </div>
            <div class="climate-click-button__body">
                <div class="climate-click-button__label">
                    <div>'
                    . __('Fund Climate Action', 'south-pole-climate-click') .
                    '<a href="javascript:void(0)" class="climate-click-button__tooltip" onclick="showPopup(this, event); return;">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" viewBox="0 0 24 24"><defs><path d="M12 7c.5523 0 1 .4477 1 1s-.4477 1-1 1-1-.4477-1-1 .4477-1 1-1zm1 9c0 .5523-.4477 1-1 1s-1-.4477-1-1v-5c0-.5523.4477-1 1-1s1 .4477 1 1v5zm11-4c0 6.6274-5.3726 12-12 12S0 18.6274 0 12 5.3726 0 12 0s12 5.3726 12 12zM12 2C6.4772 2 2 6.4772 2 12s4.4772 10 10 10 10-4.4772 10-10S17.5228 2 12 2z" id="icons-default-info"></path></defs><use xlink:href="#icons-default-info" fill="#758CA3" fill-rule="evenodd"></use></svg>
                    </a>
                    <div class="climate-click-button__content climate-click-popup">
                    <div class="climate-click-button__content--hide-popup" onclick="hidePopup(this, event);return;"></div>
                    <div class="climate-click__content contents">
                        <div class="climate-click-button__content--close-icon" onclick="closeFunction()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18" stroke="black" stroke-width="2"></line><line x1="6" y1="6" x2="18" y2="18" stroke="black" stroke-width="2"></line></svg>
                        </div>
                        <div class="climate-click-button__content--popup-compensate">
                            <div class="climate-click-button__content--popup-image">
                                <img src="' . $pluginDirectoryUrl . 'assets/images/climate-tree.png' . '"/>
                            </div>
                            <div class="climate-click-button__content--popup-text">
                                <p>' . __('Compensate means taking responsibility for your carbon footprint. When you choose to compensate, you`re supporting projects that combat climate change.', 'south-pole-climate-click') . ' <a id="climate-click-popup_learn-more-button" class="climate-click-button__content--learn-more" href="javascript:void(0)" onclick="learnMoreFunction()">' . __('Learn More', 'south-pole-climate-click') . '</a></p>
                            </div>
                        </div>
                        <span id="climate-click-popup_learn-more-dots">...</span>
                        <div id="climate-click-popup_projects" class="climate-click-button__popup-projects">
                            <div class="climate-click-button__popup-projects--header">
                                <span>' . __('We`re supporting Climate Projects', 'south-pole-climate-click') . '</span>
                            </div>
                            <div class="climate-click-button__popup-projects--body">
                                <p class="climate-click-button__popup-projects--body-title"><b>' . __('Forest Conservation (REDD+; IFM)', 'south-pole-climate-click') . '</b></p>
                                <p class="climate-click-button__popup-projects--body-description">' . __('Cease deforestation and reduce global emissions. We support Verra-verified climate initiatives in across the globe, safeguarding diverse forests and vulnerable species.', 'south-pole-climate-click') . '</p>
                            </div>
                            <div class="climate-click-button__popup-projects--footer">
                                <div class="climate-click-button__popup-projects--footer-left">
                                    <div class="climate-click-button__popup-projects--registry">
                                        <span>' . __('Registry', 'south-pole-climate-click') . '</span>
                                        <b>' . __('Verra', 'south-pole-climate-click') . '</b>
                                    </div>
                                    <div class="climate-click-button__popup-projects--standards">
                                        <span>' . __('Standards', 'south-pole-climate-click') . '</span>
                                        <img src="' . $pluginDirectoryUrl . 'assets/images/verified-carbon.png' . '"/>
                                    </div>
                                </div>
                                <div class="climate-click-button__popup-projects--footer-right">
                                    <div class="climate-click-button__popup-projects--goals">
                                        <div><span>' . __('Sustainable Development Goals', 'south-pole-climate-click') . '</span></div>
                                        <div class="climate-click-button__popup-projects--images">
                                            <img src="' . $pluginDirectoryUrl . 'assets/images/dev-goal13.png' . '"/>
                                            <img src="' . $pluginDirectoryUrl . 'assets/images/dev-goal15.png' . '"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="climate-click-button__popup-projects--body">
                                <p class="climate-click-button__popup-projects--body-title"><b>' . __('Deforestation/Reforestation', 'south-pole-climate-click') . '</b></p>
                                <p class="climate-click-button__popup-projects--body-description">' . __('Preservation of forests is crucial. Our Verra-verified climate initiatives conciously oversee vast expanses of land, safeguarding habitats, and increase carbon sequestration.', 'south-pole-climate-click') . '</p>
                            </div>
                            <div class="climate-click-button__popup-projects--footer">
                                <div class="climate-click-button__popup-projects--footer-left">
                                    <div class="climate-click-button__popup-projects--registry">
                                        <span>' . __('Registry', 'south-pole-climate-click') . '</span>
                                        <b>' . __('Verra', 'south-pole-climate-click') . '</b>
                                    </div>
                                    <div class="climate-click-button__popup-projects--standards">
                                        <span>' . __('Standards', 'south-pole-climate-click') . '</span>
                                        <img src="' . $pluginDirectoryUrl . 'assets/images/verified-carbon.png' . '"/>
                                    </div>
                                </div>
                                <div class="climate-click-button__popup-projects--footer-right">
                                    <div class="climate-click-button__popup-projects--goals">
                                        <div><span>' . __('Sustainable Development Goals', 'south-pole-climate-click') . '</span></div>
                                        <div class="climate-click-button__popup-projects--images">
                                            <img src="' . $pluginDirectoryUrl . 'assets/images/dev-goal13.png' . '"/>
                                            <img src="' . $pluginDirectoryUrl . 'assets/images/dev-goal15.png' . '"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="climate-click-button__popup-projects--body">
                                <p class="climate-click-button__popup-projects--body-title"><b>' . __('Empowering Communities', 'south-pole-climate-click') . '</b></p>
                                <p class="climate-click-button__popup-projects--body-description">' . __('Transform the way we cook for a low-carbon future. Our Verra & Gold Standard initiatives curb CO2 emissions, empower women, and advocate for cleaner air.', 'south-pole-climate-click') . '</p>
                            </div>
                            <div class="climate-click-button__popup-projects--footer">
                                <div class="climate-click-button__popup-projects--footer-left">
                                    <div class="climate-click-button__popup-projects--registry">
                                        <span>' . __('Registry', 'south-pole-climate-click') . '</span>
                                        <b>' . __('Verra', 'south-pole-climate-click') . '</b>
                                    </div>
                                    <div class="climate-click-button__popup-projects--standards">
                                        <span>' . __('Standards', 'south-pole-climate-click') . '</span>
                                        <img src="' . $pluginDirectoryUrl . 'assets/images/verified-carbon.png' . '"/>
                                    </div>
                                </div>
                                <div class="climate-click-button__popup-projects--footer-right">
                                    <div class="climate-click-button__popup-projects--goals">
                                        <div><span>' . __('Sustainable Development Goals', 'south-pole-climate-click') . '</span></div>
                                        <div class="climate-click-button__popup-projects--images">
                                            <img src="' . $pluginDirectoryUrl . 'assets/images/dev-goal3.png' . '"/>
                                            <img src="' . $pluginDirectoryUrl . 'assets/images/dev-goal7.png' . '"/>
                                            <img src="' . $pluginDirectoryUrl . 'assets/images/dev-goal13.png' . '"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a class="climate-click-button__content--show-more" target="_blank" href="https://www.southpole.com/projects">' . __('See more details >', 'south-pole-climate-click') . '</a>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
                <div class="climate-click-button__surcharge">
                <label for="climate-click-surcharge-checkbox">' . $currency . '' . $surcharge . '</label>
                </div>
            </div>
            <div class="climate-click-button__logo">
                <img src="' . $pluginDirectoryUrl . '/assets/images/climate-click.png" />
            </div>
        </div>';

        echo $climateClickSurchargeBlock;
    }

    /**
     * Get surcharge values
     *
     * @since    1.0.0
     */
    public function get_surcharge_value()
    {
        $cartItems = WC()->cart->get_cart();
        $currency = get_option('woocommerce_currency');
        $surcharge = 0;

        if ($cartItems) {
            $climateClickSurchargeCartValue = [];

            foreach ($cartItems as $cartItem) {
                if ($cartItem['data']->get_price() != 0) {
                    $productName = $cartItem['data']->get_name();
                    $price = $cartItem['data']->get_price();
                    $quantity = $cartItem['quantity'];
                    $totalPrice = $price * $quantity;
                    $priceInCents = (int)round($totalPrice * 100);
                    $userId = get_current_user_id();
                    $climateClickApiKey = get_option('climate_click_api_key');

                    $url = API_URL . "EstimateProductFootprint";

                    $params = [
                        "name" => $productName,
                        "unitPriceInCents" => $priceInCents,
                        "currency" => $currency,
                    ];
                    $headers = array(
                        'accept: application/json',
                        'content-type: application/json',
                        'X-Api-Key: ' . $climateClickApiKey
                    );

                    $response = $this->api_curl->api_response($url, $headers, "PUT", json_encode($params));

                    if ($response) {
                        $resultArray = json_decode($response, true);
                        if (isset($resultArray['priceInCents'])) {
                            $priceInCents = (float)($resultArray['priceInCents'] / 100);
                            $surcharge = $surcharge + $priceInCents;

                            $productId = $cartItem['product_id'];

                            $climateClickSurchargeCartValue[$productId] = $priceInCents;
                        }
                    }
                }
            }

            WC()->session->set('climateClickSurchargeCartValue', $climateClickSurchargeCartValue);
        }

        return $surcharge;
    }

    /**
     * Add Co2 surcharge block script for checkout and cart page
     *
     * @since    1.0.0
     */
    public function add_surcharge_block_script()
    {
        if (is_checkout()) { ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('.climate_click_surcharge_checkbox').click(function () {
                        jQuery('body').trigger('update_checkout');
                    });
                });
            </script>
            <?php
        }

        if (is_wc_endpoint_url('order-received') && WC()->session->__isset('climateClickSurcharge')) {
            WC()->session->__unset('climateClickSurcharge');
        } elseif (is_cart() || is_checkout()) {
            ?>
            <script type="text/javascript">
                jQuery(function ($) {
                    if (typeof woocommerce_params === 'undefined')
                        return false;

                    var surchargeCheckboxSelector = 'input[name=climate_click_surcharge_cart],input[name=climate_click_surcharge_checkbox]';

                    $(document.body).on('change', surchargeCheckboxSelector, function () {
                        var surcharge_checkbox_value = $(surchargeCheckboxSelector).is(':checked') ? '1' : '';

                        $.ajax({
                            type: 'POST',
                            url: woocommerce_params.ajax_url,
                            data: {
                                'action': 'store_surcharge_checkbox_value',
                                'surcharge_checkbox_value': surcharge_checkbox_value,
                            },
                            success: function (response) {
                                setTimeout(function () {
                                    <?php if (is_cart()) { ?>
                                    $(document.body).trigger('added_to_cart');
                                    <?php } else { ?>
                                    $(document.body).trigger('update_checkout');
                                    <?php } ?>
                                }, 500);
                            },
                        });
                    });
                });

            </script>
            <script type="text/javascript">
                function showPopup(obj, event) {
                    obj.classList.add('open');
                    document.querySelector('.climate-click-button__content').classList.add('open');
                }

                function hidePopup(obj, event) {
                    obj.classList.remove('open');
                    document.querySelector('.climate-click-button__content').classList.remove('open');
                }

                function closeFunction() {
                    var element = document.querySelector(".climate-click-button__content");
                    element.classList.remove("open");
                }
            </script>
            <script>
                function learnMoreFunction() {
                    var climateClickLearnMoreDots = document.getElementById("climate-click-popup_learn-more-dots");
                    var climateClickPopupContent = document.getElementById("climate-click-popup_projects");
                    var climateClickLearnMoreText = document.getElementById("climate-click-popup_learn-more-button");

                    if (climateClickLearnMoreDots.style.display === "none") {
                        climateClickLearnMoreDots.style.display = "inline";
                        climateClickPopupContent.style.display = "none";
                    } else {
                        climateClickLearnMoreDots.style.display = "none";
                        climateClickLearnMoreText.innerHTML = "";
                        climateClickPopupContent.style.display = "inline";
                    }
                }

            </script>
            <?php
        }
    }

    /**
     * Add surcharge values in the cart total
     *
     * @since    1.0.0
     */
    public function add_surcharge_in_cart_total($cart)
    {
        if (isset($_POST['post_data'])) {
            parse_str($_POST['post_data'], $post_data);
        } else {
            $post_data = $_POST;
        }

        $surchargeValue = $this->get_surcharge_value();
        $surchargeLabel = sprintf(__('Climate Action', 'south-pole-climate-click'));
        $checkoutPage = get_option('wc_climate_click_settings_tabs_page_selection');

        if (is_checkout()) {
            $surcharge_checkout = isset($post_data['climate_click_surcharge_checkbox']) ? true : false;
            $climateClickSurcharge = WC()->session->get('climateClickSurcharge');
            if (empty($climateClickSurcharge)) {
                WC()->session->set('climateClickSurcharge', $surcharge_checkout);
            }
        }

        if (
            (isset($post_data['climate_click_surcharge_checkbox']) && is_checkout()) || (!empty(
                WC()->session->get(
                    'climateClickSurcharge'
                )
                ) && is_cart() && ($checkoutPage == 'cartPage' || $checkoutPage == 'checkoutCartPage')) || (!empty(
                WC()->session->get('climateClickSurcharge')
                ) && $checkoutPage == 'cartPage')
        ) {
            $cart->add_fee($surchargeLabel, $surchargeValue);
        }

        if (!$_POST || (is_admin() && !is_ajax())) {
            return;
        }
    }

    /**
     * Ajax response for the cart page
     *
     * @since    1.0.0
     */
    public function store_surcharge_checkbox_value()
    {
        if (isset($_POST['surcharge_checkbox_value'])) {
            $surcharge_checkbox_value = $_POST['surcharge_checkbox_value'] ? true : false;
            // Set to a WC Session variable
            WC()->session->set('climateClickSurcharge', $surcharge_checkbox_value);

            echo $surcharge_checkbox_value ? '1' : '0';
            die();
        }
    }

    /**
     * Add surcharge value in the order meta
     *
     * @since    1.0.0
     */
    public function add_surcharge_values_to_order_item_meta($item_id, $values)
    {
        if (!empty(WC()->session->get('climateClickSurcharge'))) {
            $surcharge_session = WC()->session->get('climateClickSurchargeCartValue');

            $productId = $values['product_id'];
            $surcharge_value = $surcharge_session[$productId];
            $user_custom_values = [
                "productId" => $productId,
                "surchargeValue" => $surcharge_value,
            ];

            if (!empty($user_custom_values)) {
                wc_add_order_item_meta($item_id, 'climate_click_surcharge', json_encode($user_custom_values));
            }
        }
    }

    /**
     * Hide custom order meta
     *
     * @since    1.0.0
     */
    public function hide_surcharge_item_meta($formatted_meta, $item)
    {
        foreach ($formatted_meta as $key => $meta) {
            if (in_array($meta->key, array('climate_click_surcharge'))) {
                unset($formatted_meta[$key]);
            }
        }

        return $formatted_meta;
    }

    /**
     * Call Api after order placed
     *
     * @since    1.0.0
     */
    public function compensate_order_after_process($order_id)
    {
        if (!empty(WC()->session->get('climateClickSurcharge'))) {
            $order = wc_get_order($order_id);

            if ($order) {
                $userId = get_current_user_id();
                $currency = $order->get_currency();
                $surcharge = $this->get_surcharge_value();
                $surchargePriceInCents = (int)round($surcharge * 100);
                $climateClickApiKey = get_option('climate_click_api_key');
                $url = API_URL . "CompensateOrder";

                $params = [
                    "currency" => $currency,
                    "orderId" => strval($order_id),
                    "compensationAmountInCents" => $surchargePriceInCents,
                ];

                $headers = array(
                    'accept: application/json',
                    'content-type: application/json',
                    'X-Api-Key: ' . $climateClickApiKey
                );

                $this->api_curl->api_response($url, $headers, "PUT", json_encode($params));
            }
        }
    }
}
