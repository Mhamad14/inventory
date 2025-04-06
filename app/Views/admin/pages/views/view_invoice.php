<?php

use Config\App;
require_once(APPPATH . 'ThirdParty/Tcpdf/config/tcpdf_config.php');

$description = $order[0]['description'] != '' ?  $order[0]['description'] : '';
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetTitle(labels('invoice_label', 'Invoice - ' . $description));

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, $description);

// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
$pdf->SetFont('helvetica', '', 11, '', true);

// ---------------------------------------------------------

// add a page
$pdf->AddPage();

//Generate HTML table data from MySQL - end
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 11, '', true);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);

$pdf->SetXY(20, 40);
$pdf->Multicell(
    0, 0,
    $order[0]['address'] . "\n" .
    'Mo' . $order[0]['contact'] . "\n" .
    $order[0]['warehouse_name'] . "\n" .
    ucwords($order[0]['b_tax']) . "-" . $order[0]['tax_value'],
    0, 'L', 1, 1, '', '', true, 0, false, true, 0
);
$pdf->SetFont('helvetica', 'B', 11, '', true);


$pdf->SetXY(80, 40);
$pdf->Multicell(0, 0, 'Billing Details', 0, 'L', 1, 1, '', '', true, 0, false, true, 0);
$pdf->SetFont('helvetica', '', 11, '', true);

$pdf->SetXY(80, 47);
$pdf->Multicell(0, 0, $order[0]['first_name'] . "\n" . $order[0]['email'] . "\n" . 'Mo.' . $order[0]['mobile'], 0, 'L', 1, 1, '', '', true, 0, false, true, 0);
$pdf->SetFont('helvetica', '', 11, '', true);

$pdf->SetXY(140, 40);
$pdf->Multicell(150, 0, 'Invoice No', 0, 'L', 1, 1, '', '', true, 0, false, true, 0);
$pdf->SetFont('helvetica', '', 11, '', true);

$pdf->SetXY(140, 47);
$pdf->Multicell(0, 0, "#INVOC - " . $order[0]['id'], 0, 'L', 1, 1, '', '', true, 0, false, true, 0);
$pdf->SetFont('helvetica', '', 11, '', true);

$pdf->SetXY(140, 53);
$pdf->Multicell(0, 0, 'Invoice Date', 0, 'L', 1, 1, '', '', true, 0, false, true, 0);

$statustext = 'No payments found!';
if ($order[0]['payment_status'] == "fully_paid") {
    $color = array(40, 167, 69);
    $statustext = 'FULLY PAID';
} elseif ($order[0]['payment_status'] == "partially_paid") {
    $color = array(255, 165, 0);
    $statustext =  'PARTIALLY PAID';
} elseif ($order[0]['payment_status'] == "unpaid") {
    $color = array(58, 186, 244);
    $statustext = 'UNPAID';
} elseif ($order[0]['payment_status'] == "cancelled") {
    $color = array(252, 84, 75);
    $statustext = 'CANCELLED';
} else {
    $color = array(0, 0, 255);
    $statustext = 'N/A';
}

$pdf->SetXY(20, 30);
$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color));
$pdf->MultiCell(40, 4, $statustext, 1, 'C', 1, 0);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 11, '', true);
$pdf->SetXY(140, 59);
$pdf->Multicell(0, 0, date_formats(strtotime($order[0]['created_at'])), 0, 'L', 1, 1, '', '', true, 0, false, true, 0);
$y = $pdf->GetY();
$pdf->SetFont('helvetica', 'B', 11, '', true);

$pdf->SetXY(90, $y + 5);

$table = new \CodeIgniter\View\Table();

$template = array(
    'table_open' => '<table bsalesinfo="1" cellpadding="2" cellspacing="1">'
);

$table->setTemplate($template);

$table->setHeading('Order Type', 'Name', 'Price', 'Tax', 'Tax Amount', 'Quantity', 'Subtotal');

foreach ($order as $o) :
    if (isset($o['service_name']) && !empty($o['service_name'])) {
        $item_name = $o['service_name'];
    }
    if (isset($o['product_name']) && !empty($o['product_name'])) {
        $item_name = $o['product_name'] ? $o['product_name'] : $o['pro_name'];
    }

    $tax_details = json_decode($o['tax_details'], true);
    $tax_amount = empty($o['tax_name']) ? 0 : $o['price'] / (1 + $o['tax_percentage']);
    $tax_amount_html = '<span>' . htmlspecialchars($o['tax_name']) . ' : ' . currency_location(number_format($tax_amount, 2)) . '</span><br>';
    $tax_amount_html .= '<span><strong>Total Tax: ' . currency_location(number_format($tax_amount, 2)) . '</strong></span>';

    $price =  $o['price'] -   $tax_amount;

    $tax = empty($o['tax_name']) ?  "- - -"    : $o['tax_name'] . " - " . $o['tax_percentage'] . "%";


    if (empty($tax_details)) {
        $table->addRow(ucwords($o['order_type']), $item_name, currency_location(number_format($price, 2)), $tax, $tax_amount_html, $o['quantity'], currency_location(number_format($o['sub_total'], 2)));
    } else {

        $tax_name = '';
        $tax_amount_html = '';
        $total_tax_percentage = 0;

        // Calculate total tax percentage
        foreach ($tax_details as $tax) {
            $total_tax_percentage += $tax['percentage'];
            $tax_name .= '<span>' . htmlspecialchars($tax['name']) . ' :  ' . htmlspecialchars($tax['percentage']) . '%</span><br>';
        }

        // Calculate original amount (before tax)
        $original_amount = $o['price'] / (1 + $total_tax_percentage / 100);
        $total_tax_amount = 0;

        // Calculate each tax amount and append HTML
        foreach ($tax_details as $tax) {
            $tax_percentage = $tax['percentage'];
            $tax_amount = $original_amount * $tax_percentage / 100;
            $tax_amount_html .= '<span>' . htmlspecialchars($tax['name']) . ' : ' . currency_location(number_format($tax_amount, 2)) . '</span><br>';
            $total_tax_amount += $tax_amount;
        }

        // Add total tax HTML
        $tax_amount_html .= '<span><strong>Total Tax: ' . currency_location(number_format($total_tax_amount, 2)) . '</strong></span>';

        // Populate $rows array
        $price = $o['price'] -  $total_tax_amount;

        $table->addRow(ucwords($o['order_type']), $item_name, currency_location(number_format($price, 2)), $tax_name, $tax_amount_html, $o['quantity'], currency_location(number_format($o['sub_total'], 2)));
    }


endforeach;
$table->addRow("", "", "", "", "<strong>Total</strong>", "<strong>" . currency_location(number_format($o['total'], 2)) . "</strong>");


$html = $table->generate();



// output the HTML content
$pdf->SetXY(8, 80);
$pdf->SetFont('helvetica', '', 9, '', true);

$pdf->writeHTML($html, true, 0, true, 0);
// reset pointer to the last page

$pdf->Ln(10);

$pdf->lastPage();
$pdf->Multicell(35, 0, 'Invoice Summary', 0, 'J', 1, 1, '', '', true, 0, false, true, 0);
$pdf->Multicell(35, 10, $statustext, 0, 'J', 1, 1, '', '', true, 0, false, true, 0);


$pdf->Ln(20);

$pdf->SetXY(132, 130);
$pdf->SetFont('helvetica', 'B', 11, '', true);
$pdf->Multicell(0, 0, "Sub Total", 0, 'L', 1, 1, '', '', true, 0, false, true, 0);

$pdf->SetXY(170, 130);
$pdf->SetFont('helvetica', '', 11, '', true);
$pdf->Multicell(0, 0, $currency . $order[0]['total'], 0, 'L', 1, 1, '', '', true, 0, false, true, 0);

$pdf->SetXY(132, 140);
$pdf->SetFont('helvetica', 'B', 11, '', true);
$pdf->Multicell(0, 0, "Delivery Charges", 0, 'L', 1, 1, '', '', true, 0, false, true, 0);

if ($order[0]['delivery_charges'] == 0) {
    $order[0]['delivery_charges'] = "N/A";
} else {
    $order[0]['delivery_charges'] = currency_location(decimal_points($order[0]['delivery_charges']));
}
$pdf->SetXY(170, 140);
$pdf->SetFont('helvetica', '', 11, '', true);
$pdf->Multicell(0, 0, $order[0]['delivery_charges'], 0, 'L', 1, 1, '', '', true, 0, false, true, 0);

$pdf->SetXY(132, 150);
$pdf->SetFont('helvetica', 'B', 11, '', true);
$pdf->Multicell(0, 0, "Discount", 0, 'L', 1, 1, '', '', true, 0, false, true, 0);

if ($order[0]['discount'] == 0) {
    $order[0]['discount'] = "N/A";
} else {
    $order[0]['discount'] = currency_location(decimal_points($order[0]['discount']));
}
$pdf->SetXY(170, 150);
$pdf->SetFont('helvetica', '', 11, '', true);
$pdf->Multicell(0, 0, $order[0]['discount'], 0, 'L', 1, 1, '', '', true, 0, false, true, 0);

$pdf->SetXY(132, 160);
$pdf->SetFont('helvetica', 'B', 11, '', true);
$pdf->Multicell(0, 0, "Total", 0, 'L', 1, 1, '', '', true, 0, false, true, 0);


$pdf->SetXY(170, 160);
$pdf->SetFont('helvetica', '', 11, '', true);
$pdf->Multicell(0, 0, currency_location(decimal_points($order[0]['final_total'])), 0, 'L', 1, 1, '', '', true, 0, false, true, 0);

$path = FCPATH . "public\invoice\invoice";
$pdf->Output($path . $order[0]['id'] . '.pdf', 'F');
$myFile = $path . $order[0]['id'] . ".pdf";
if (file_exists($myFile)) {
    $pdf->Output('inv-' . $order[0]['id'] . '.pdf', 'D');
}
