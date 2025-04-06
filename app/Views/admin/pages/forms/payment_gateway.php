<!-- main content form -->
<div class="main-content">
    <section class="section">
            <div class="section-header">
                <h1><?= labels('payment_gateway', 'Payment Gateway') ?> <?= labels('settings', 'Settings') ?></h1>
            </div>

            <?php
            $session = session();
            if ($session->has('message')) { ?>
                <div class="text-danger"><?php $message = session('message');
                                            echo $message['title']; ?></label></div>
            <?php } ?>

            <div class="section-body">
                <div class="row mt-sm-4">
                    <div class='col-md-12'>
                        <div class="card">
                            <div class="card-body">
                                <form action="<?= base_url('admin/settings/save_settings') ?>" id="payment_gateway_setting_form" accept-charset="utf-8" method="POST">
                                    <input type="hidden" class="form-control" name="setting_type" value="payment_gateway" placeholder="">
                                    <div class="row">
                                        <div class="section-title ml-3">RazorPay</div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="razorpay_payment_mode">Payment Mode</label>
                                                <select class="form-control" id="razorpay_payment_mode" name="razorpay_payment_mode" value="<?= !empty($payment_gateway) && !empty($payment_gateway['razorpay_payment_mode']) ? $payment_gateway['razorpay_payment_mode'] : "" ?>">
                                                    <option>Select Mode</option>
                                                    <option selected>Test</option>
                                                    <option>Live</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="razorpay_secret_key">Secret Key</label>
                                                <?php if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) { ?>
                                                    <input type="text" class="form-control" name="razorpay_secret_key" id="razorpay_secret_key" value="************">
                                                <?php } else { ?>
                                                    <input type="text" class="form-control" id="razorpay_secret_key" name="razorpay_secret_key" value="<?= !empty($payment_gateway) && !empty($payment_gateway['razorpay_secret_key']) ? $payment_gateway['razorpay_secret_key'] : "" ?>">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="razorpay_api_key">API Key</label>
                                                <?php if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) { ?>
                                                    <input type="text" class="form-control" name="razorpay_api_key" id="razorpay_api_key" value="************">
                                                <?php } else { ?>
                                                    <input type="text" class="form-control" name="razorpay_api_key" id="razorpay_api_key" value="<?= !empty($payment_gateway) && !empty($payment_gateway['razorpay_api_key']) ? $payment_gateway['razorpay_api_key'] : "" ?>">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>status</label>
                                            <div class="selectgroup w-100">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="razorpay_status" id="active" value="1" class="selectgroup-input" checked="">
                                                    <span class="selectgroup-button">Active</span>
                                                </label>
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="razorpay_status" id="deactive" value="0" class="selectgroup-input">
                                                    <span class="selectgroup-button">Deactive</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row">
                                        <div class="section-title ml-3">Stripe</div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="stripe_payment_mode">Payment Mode</label>
                                                <select class="form-control" name="stripe_payment_mode" id="stripe_payment_mode" value="<?= !empty($payment_gateway) && !empty($payment_gateway['stripe_payment_mode']) ? $payment_gateway['stripe_payment_mode'] : "" ?>">
                                                    <option>Select Mode</option>
                                                    <option selected>Test</option>
                                                    <option>Live</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="stripe_currency_symbol">Currency Symbol</label>
                                                <select class="form-control" name="stripe_currency_symbol" id="stripe_currency_symbol" value="<?= !empty($payment_gateway) && !empty($payment_gateway['stripe_currency_symbol']) ? $payment_gateway['stripe_currency_symbol'] : "" ?>">
                                                    <option value="">Select Currency Code </option>
                                                    <option value="INR" selected="">Indian rupee </option>
                                                    <option value="USD">United States dollar </option>
                                                    <option value="AED">United Arab Emirates Dirham </option>
                                                    <option value="AFN">Afghan Afghani </option>
                                                    <option value="ALL">Albanian Lek </option>
                                                    <option value="AMD">Armenian Dram </option>
                                                    <option value="ANG">Netherlands Antillean Guilder </option>
                                                    <option value="AOA">Angolan Kwanza </option>
                                                    <option value="ARS">Argentine Peso</option>
                                                    <option value="AUD"> Australian Dollar</option>
                                                    <option value="AWG"> Aruban Florin</option>
                                                    <option value="AZN"> Azerbaijani Manat </option>
                                                    <option value="BAM"> Bosnia-Herzegovina Convertible Mark </option>
                                                    <option value="BBD"> Bajan dollar </option>
                                                    <option value="BDT"> Bangladeshi Taka</option>
                                                    <option value="BGN"> Bulgarian Lev </option>
                                                    <option value="BIF">Burundian Franc</option>
                                                    <option value="BMD"> Bermudan Dollar</option>
                                                    <option value="BND"> Brunei Dollar </option>
                                                    <option value="BOB"> Bolivian Boliviano </option>
                                                    <option value="BRL"> Brazilian Real </option>
                                                    <option value="BSD"> Bahamian Dollar </option>
                                                    <option value="BWP"> Botswanan Pula </option>
                                                    <option value="BZD"> Belize Dollar </option>
                                                    <option value="CAD"> Canadian Dollar </option>
                                                    <option value="CDF"> Congolese Franc </option>
                                                    <option value="CHF"> Swiss Franc </option>
                                                    <option value="CLP"> Chilean Peso </option>
                                                    <option value="CNY"> Chinese Yuan </option>
                                                    <option value="COP"> Colombian Peso </option>
                                                    <option value="CRC"> Costa Rican Colón </option>
                                                    <option value="CVE"> Cape Verdean Escudo </option>
                                                    <option value="CZK"> Czech Koruna </option>
                                                    <option value="DJF"> Djiboutian Franc </option>
                                                    <option value="DKK"> Danish Krone </option>
                                                    <option value="DOP"> Dominican Peso </option>
                                                    <option value="DZD"> Algerian Dinar </option>
                                                    <option value="EGP"> Egyptian Pound </option>
                                                    <option value="ETB"> Ethiopian Birr </option>
                                                    <option value="EUR"> Euro </option>
                                                    <option value="FJD"> Fijian Dollar </option>
                                                    <option value="FKP"> Falkland Island Pound </option>
                                                    <option value="GBP"> Pound sterling </option>
                                                    <option value="GEL"> Georgian Lari </option>
                                                    <option value="GIP"> Gibraltar Pound </option>
                                                    <option value="GMD"> Gambian dalasi </option>
                                                    <option value="GNF"> Guinean Franc </option>
                                                    <option value="GTQ"> Guatemalan Quetzal </option>
                                                    <option value="GYD"> Guyanaese Dollar </option>
                                                    <option value="HKD"> Hong Kong Dollar </option>
                                                    <option value="HNL"> Honduran Lempira </option>
                                                    <option value="HRK"> Croatian Kuna </option>
                                                    <option value="HTG"> Haitian Gourde </option>
                                                    <option value="HUF"> Hungarian Forint </option>
                                                    <option value="IDR"> Indonesian Rupiah </option>
                                                    <option value="ILS"> Israeli New Shekel </option>
                                                    <option value="ISK"> Icelandic Króna </option>
                                                    <option value="JMD"> Jamaican Dollar </option>
                                                    <option value="JPY"> Japanese Yen </option>
                                                    <option value="KES"> Kenyan Shilling </option>
                                                    <option value="KGS"> Kyrgystani Som </option>
                                                    <option value="KHR"> Cambodian riel </option>
                                                    <option value="KMF"> Comorian franc </option>
                                                    <option value="KRW"> South Korean won </option>
                                                    <option value="KYD"> Cayman Islands Dollar </option>
                                                    <option value="KZT"> Kazakhstani Tenge </option>
                                                    <option value="LAK"> Laotian Kip </option>
                                                    <option value="LBP"> Lebanese pound </option>
                                                    <option value="LKR"> Sri Lankan Rupee </option>
                                                    <option value="LRD"> Liberian Dollar </option>
                                                    <option value="LSL">Lesotho loti </option>
                                                    <option value="MAD"> Moroccan Dirham </option>
                                                    <option value="MDL"> Moldovan Leu </option>
                                                    <option value="MGA"> Malagasy Ariary </option>
                                                    <option value="MKD"> Macedonian Denar </option>
                                                    <option value="MMK"> Myanmar Kyat </option>
                                                    <option value="MNT"> Mongolian Tugrik </option>
                                                    <option value="MOP"> Macanese Pataca </option>
                                                    <option value="MRO"> Mauritanian Ouguiya </option>
                                                    <option value="MUR"> Mauritian Rupee</option>
                                                    <option value="MVR"> Maldivian Rufiyaa </option>
                                                    <option value="MWK"> Malawian Kwacha </option>
                                                    <option value="MXN"> Mexican Peso </option>
                                                    <option value="MYR"> Malaysian Ringgit </option>
                                                    <option value="MZN"> Mozambican metical </option>
                                                    <option value="NAD"> Namibian dollar </option>
                                                    <option value="NGN"> Nigerian Naira </option>
                                                    <option value="NIO">Nicaraguan Córdoba </option>
                                                    <option value="NOK"> Norwegian Krone </option>
                                                    <option value="NPR"> Nepalese Rupee </option>
                                                    <option value="NZD"> New Zealand Dollar </option>
                                                    <option value="PAB"> Panamanian Balboa </option>
                                                    <option value="PEN"> Sol </option>
                                                    <option value="PGK"> Papua New Guinean Kina </option>
                                                    <option value="PHP">Philippine peso </option>
                                                    <option value="PKR"> Pakistani Rupee </option>
                                                    <option value="PLN"> Poland złoty </option>
                                                    <option value="PYG"> Paraguayan Guarani </option>
                                                    <option value="QAR"> Qatari Rial </option>
                                                    <option value="RON">Romanian Leu </option>
                                                    <option value="RSD"> Serbian Dinar </option>
                                                    <option value="RUB"> Russian Ruble </option>
                                                    <option value="RWF"> Rwandan franc </option>
                                                    <option value="SAR"> Saudi Riyal </option>
                                                    <option value="SBD"> Solomon Islands Dollar </option>
                                                    <option value="SCR">Seychellois Rupee </option>
                                                    <option value="SEK"> Swedish Krona </option>
                                                    <option value="SGD"> Singapore Dollar </option>
                                                    <option value="SHP"> Saint Helenian Pound </option>
                                                    <option value="SLL"> Sierra Leonean Leone </option>
                                                    <option value="SOS">Somali Shilling </option>
                                                    <option value="SRD"> Surinamese Dollar </option>
                                                    <option value="STD"> Sao Tome Dobra </option>
                                                    <option value="SZL"> Swazi Lilangeni </option>
                                                    <option value="THB"> Thai Baht </option>
                                                    <option value="TJS"> Tajikistani Somoni </option>
                                                    <option value="TOP"> Tongan Paʻanga </option>
                                                    <option value="TRY"> Turkish lira </option>
                                                    <option value="TTD"> Trinidad &amp; Tobago Dollar </option>
                                                    <option value="TWD"> New Taiwan dollar </option>
                                                    <option value="TZS"> Tanzanian Shilling </option>
                                                    <option value="UAH"> Ukrainian hryvnia </option>
                                                    <option value="UGX"> Ugandan Shilling </option>
                                                    <option value="UYU"> Uruguayan Peso </option>
                                                    <option value="UZS"> Uzbekistani Som </option>
                                                    <option value="VND"> Vietnamese dong </option>
                                                    <option value="VUV"> Vanuatu Vatu </option>
                                                    <option value="WST"> Samoa Tala</option>
                                                    <option value="XAF"> Central African CFA franc </option>
                                                    <option value="XCD"> East Caribbean Dollar </option>
                                                    <option value="XOF"> West African CFA franc </option>
                                                    <option value="XPF"> CFP Franc </option>
                                                    <option value="YER"> Yemeni Rial </option>
                                                    <option value="ZAR"> South African Rand </option>
                                                    <option value="ZMW"> Zambian Kwacha </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="stripe_publishable_key">Stripe Publishable Key</label>

                                                <?php if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) { ?>
                                                    <input type="text" class="form-control" name="stripe_publishable_key" id="stripe_publishable_key" value="************">
                                                <?php } else { ?>
                                                    <input type="text" class="form-control" name="stripe_publishable_key" id="stripe_publishable_key" value="<?= !empty($payment_gateway) && !empty($payment_gateway['stripe_publishable_key']) ? $payment_gateway['stripe_publishable_key'] : "" ?>">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="stripe_secret_key">Stripe Secret Key</label>
                                                <?php if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) { ?>
                                                    <input type="text" class="form-control" name="stripe_secret_key" id="stripe_secret_key" value="************">
                                                <?php } else { ?>
                                                    <input type="text" class="form-control" name="stripe_secret_key" id="stripe_secret_key" value="<?= !empty($payment_gateway) && !empty($payment_gateway['stripe_secret_key']) ? $payment_gateway['stripe_secret_key'] : "" ?>">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="stripe_webhook_secret_key">Stripe Webhook Secret Key</label>
                                                <?php if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) { ?>
                                                    <input type="text" class="form-control" name="stripe_webhook_secret_key" id="stripe_webhook_secret_key" value="************">
                                                <?php } else { ?>
                                                    <input type="text" class="form-control" name="stripe_webhook_secret_key" id="stripe_webhook_secret_key" value="<?= !empty($payment_gateway) && !empty($payment_gateway['stripe_webhook_secret_key']) ? $payment_gateway['stripe_webhook_secret_key'] : "" ?>">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>status</label>
                                            <div class="selectgroup w-100">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="stripe_status" id="active" value="1" class="selectgroup-input" checked="">
                                                    <span class="selectgroup-button">Active</span>
                                                </label>
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="stripe_status" id="deactive" value="0" class="selectgroup-input">
                                                    <span class="selectgroup-button">Deactive</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row">
                                        <div class="section-title ml-3">Flutterwave</div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="flutterwave_payment_mode">Payment Mode</label>
                                                <select class="form-control" name="flutterwave_payment_mode" id="flutterwave_payment_mode" <?= !empty($payment_gateway) && !empty($payment_gateway['flutterwave_payment_mode']) ? $payment_gateway['flutterwave_payment_mode'] : "" ?>>
                                                    <option>Select Mode</option>
                                                    <option selected>Test</option>
                                                    <option>Live</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="flutterwave_currency_symbol">Currency Symbol</label>
                                                <select class="form-control" name="flutterwave_currency_symbol" id="flutterwave_currency_symbol" value="<?= !empty($payment_gateway) && !empty($payment_gateway['flutterwave_currency_symbol']) ? $payment_gateway['flutterwave_currency_symbol'] : "" ?>">
                                                    <option value="">Select Currency Code </option>
                                                    <option value="NGN" selected="">Nigerian Naira</option>
                                                    <option value="USD">United States dollar</option>
                                                    <option value="TZS">Tanzanian Shilling</option>
                                                    <option value="SLL">Sierra Leonean Leone</option>
                                                    <option value="MUR">Mauritian Rupee</option>
                                                    <option value="MWK">Malawian Kwacha </option>
                                                    <option value="GBP">UK Bank Accounts</option>
                                                    <option value="GHS">Ghanaian Cedi</option>
                                                    <option value="RWF">Rwandan franc</option>
                                                    <option value="UGX">Ugandan Shilling</option>
                                                    <option value="ZMW">Zambian Kwacha</option>
                                                    <option value="KES">Mpesa</option>
                                                    <option value="ZAR">South African Rand</option>
                                                    <option value="XAF">Central African CFA franc</option>
                                                    <option value="XOF">West African CFA franc</option>
                                                    <option value="AUD">Australian Dollar</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="flutterwave_public_key">Flutterwave Public Key</label>
                                                <?php if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) { ?>
                                                    <input type="text" class="form-control" name="flutterwave_public_key" id="flutterwave_public_key" value="************">
                                                <?php } else { ?>
                                                    <input type="text" class="form-control" name="flutterwave_public_key" id="flutterwave_public_key" value="<?= !empty($payment_gateway) && !empty($payment_gateway['flutterwave_public_key']) ? $payment_gateway['flutterwave_public_key'] : "" ?>">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="flutterwave_secret_key"> Secret Key</label>
                                                <?php if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) { ?>
                                                    <input type="text" class="form-control" name="flutterwave_secret_key" id="flutterwave_secret_key" value="************">
                                                <?php } else { ?>
                                                    <input type="text" class="form-control" name="flutterwave_secret_key" id="flutterwave_secret_key" value="<?= !empty($payment_gateway) && !empty($payment_gateway['flutterwave_secret_key']) ? $payment_gateway['flutterwave_secret_key'] : "" ?>">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="flutterwave_encryption_key">Flutterwave Encryption key</label>
                                                <?php if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) { ?>
                                                    <input type="text" class="form-control" name="flutterwave_encryption_key" id="flutterwave_encryption_key" value="************">
                                                <?php } else { ?>
                                                    <input type="text" class="form-control" name="flutterwave_encryption_key" id="flutterwave_encryption_key" value="<?= !empty($payment_gateway) && !empty($payment_gateway['flutterwave_encryption_key']) ? $payment_gateway['flutterwave_encryption_key'] : "" ?>">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>status</label>
                                            <div class="selectgroup w-100">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="flutterwave_status" id="active" value="1" class="selectgroup-input" checked="">
                                                    <span class="selectgroup-button">Active</span>
                                                </label>
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="flutterwave_status" id="deactive" value="0" class="selectgroup-input">
                                                    <span class="selectgroup-button">Deactive</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <input type="submit" name="update" id="update" value="<?= labels('update', 'Update') ?>" class="btn btn-primary">
                                                <input type="reset" name="clear" id="clear" value="<?= labels('clear', 'Clear') ?>" class="btn btn-info">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>