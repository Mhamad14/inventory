<html>
<!-- <pre> 
 -->



<style>
    @media print {
        /* body * {
            visibility: hidden;
        } */

        #section-not-to-print,
        #section-not-to-print * {
            display: none;

        }

        #section-to-print,
        #section-to-print * {
            visibility: visible;
        }

        #section-to-print {
            position: relative;
            /* justify-content: left; */
            left: 0;
            top: 0;
            font-size: small;

        }
    }

    table {
        border-collapse: collapse;
        width: 100%;

    }

    th,
    td {
        padding: 4px;
        text-align: left;
        border-bottom: 1px solid #000000d4;
    }



    .button {
        background-color: #4CAF50;
        /* Green */
        border: none;
        color: white;
        padding: 16px 32px;
        text-align: left;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        transition-duration: 0.4s;
        cursor: pointer;
        background-color: white;
        color: black;
        border: 2px solid #e7e7e7;
    }

    .button4:hover {
        background-color: #e7e7e7;
    }
</style>

<head>
    <?php
    $settings = get_settings('general', true);
    $favicon = (isset($settings['favicon'])) ? $settings['favicon'] : "";
    $data['company'] = (isset($settings['title'])) ? $settings['title'] : "UpBiz";
    ?>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title><?= $data['company'] ?> - Thermal Print</title>
    <link rel="icon" href="<?= base_url() . $favicon ?>" type="image/gif" sizes="16x16">


    <head>
        <title>Thermal Invoice</title>
        <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
        <link rel="icon" href="<?= base_url() . get_settings('favicon') ?>" type="image/gif" sizes="16x16">
    </head>
    <!--Get your own code at fontawesome.com-->

<body style="font-family:Verdana; font-weight:bolder">
    <!--  -->
    <div id="section-to-print">
        <address style="text-align: center;">
            <strong><?= $name ?></strong><br>
            <b>Order No : </b>#
            <?= $order[0]['id'] ?>
            <br> <b>Date: </b>
            <?= date("d-m-Y, g:i A - D", strtotime($order[0]['created_at'])) ?>
            <br>
            <?php

            if (isset($order[0]['b_tax']) && !empty($order[0]['b_tax'])) { ?>
                <b><?= $order[0]['b_tax'] ?></b> : <?= $order[0]['tax_value'] ?>%<br>

            <?php } ?>
            <hr>
            <!-- ------------------------------------------------------- -->

        </address>
        <table style="margin-bottom: 5vh; font-size:11px; font-weight : bold; margin-top:2vh">
            <tr>
                <td>
                    <div>Delivery Address<address>
                            <?= ($order[0]['first_name'] != "") ? $order[0]['first_name'] : $order[0]['first_name'] ?><br>
                            <?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($order[0]['mobile']) - 3) . substr($order[0]['mobile'], -3) : $order[0]['mobile']; ?><br>
                            <?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($order[0]['email']) - 3) . substr($order[0]['email'], -3) : $order[0]['email']; ?>ss<br>
                        </address>
                    </div>
                </td>
            </tr>
        </table>
        <div style="margin-top: 4vh;"><b>Product Details:</b></div>
        <div>
            <table style="font-size: 14px;">
                <tr>
                    <!-- <th>Sr No.</th>
                            <th>Product Code</th> -->
                    <th>Name</th>
                    <th>Price</th>
                    <!-- <th class="d-none">Tax (%)</th> -->
                    <!-- <th class="d-none">Tax Amount (₹)</th> -->
                    <th>Qty</th>
                    <th>SubTotal (<?= $currency ?>)</th>
                </tr>
                <?php $totalQuantity = 0;
                foreach ($order as $item) : ?>
                    <?php $totalQuantity += $item['quantity']; ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td><?php echo $item['price']; ?></td>
                        <td><?php echo $item['quantity'] ?></td>
                        <td><?= $item['sub_total'] ?></td>
                    </tr>
                    <tr>
                    <?php endforeach; ?>
                    <th></th>
                    <th>Total</th>
                    <th> <?= $totalQuantity ?>
                        <br>
                    </th>
                    <th> <?= $currency . ' ' . $order[0]['total'] ?><br></th>
                    </tr>
            </table>
        </div>
        <br>
        <div>
        </div>
        <div>
            <p><b>Payment Method : </b> <?= $order[0]['payment_method'] ?></p>
        </div>
        <div>
            <table align="right" style="width: 100%; font-size: 14px;">
                <?php
                $tax_per = ($order[0]['tax_percentage']);
                $tax_name = ($order[0]['tax_name']);
                $tax_amount = $tax_amount = intval($order[0]['total']) * ($tax_per / 100);
                ?>
                <tr>
                    <th></th>
                </tr>
                <tr class="">
                    <th>Tax <?= $tax_name ?> (<?= $tax_per ?>%)</th>
                    <td>+
                        <?php
                        echo $currency . ' ' . number_format($tax_amount, 2); ?>
                    </td>
                </tr>
                <tr>
                    <th>Delivery Charge (<?= $currency ?>)</th>
                    <td>+ <?php $total = 0;
                            $total += $order[0]['delivery_charges'];
                            echo number_format($order[0]['delivery_charges'], 2); ?>
                    </td>
                </tr>
                <?php
                if (isset($order[0]['discount']) && $order[0]['discount'] > 0 && $order[0]['discount'] != NULL) { ?>
                    <tr>
                        <th>Discount </th>
                        <td>- <?php echo $order[0]['discount']
                                ?>
                        </td>
                    </tr>
                <?php
                }
                ?>
                <tr>
                    <th>Total Order Price(<?= $currency ?>)<br /><small> (including taxes , discount) </small></th>
                    <td>+ <strong> <?= number_format($order[0]['final_total'], 2) ?> </strong></td>
                </tr>
            </table>
        </div>
        <div style="margin: 125px;"></div>
        <div style="margin-top: 200px ">
            <hr>
            <p align="center">Thank You, Visit Us Again!</p>

            <div id="section-not-to-print">
                <button type='button' value='Print this page' onclick='{window.print()};' class="button button4"><i class="fas fa-print"></i> Print</button>
            </div>
        </div>
        <!-- /.container-fluid -->

    </div>

</body>

</html>