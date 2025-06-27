<?php

use Config\App;
require_once(APPPATH . 'ThirdParty/Tcpdf/config/tcpdf_config.php');

// Helper function to safely set font with Kurdish support
function setKurdishFont($pdf, $style = '', $size = 11) {
    // Check if dejavusans font is available
    $font_path = APPPATH . 'ThirdParty/Tcpdf/fonts/dejavusans.php';
    if (file_exists($font_path)) {
        try {
            $pdf->SetFont('dejavusans', $style, $size, '', true);
            return true;
        } catch (Exception $e) {
            // Fallback to helvetica if dejavusans fails
            $pdf->SetFont('helvetica', $style, $size, '', true);
            return false;
        }
    } else {
        // Use helvetica if dejavusans not available
        $pdf->SetFont('helvetica', $style, $size, '', true);
        return false;
    }
}

$description = $order[0]['description'] != '' ?  $order[0]['description'] : '';

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetTitle(labels('purchase_invoice_label', 'Purchase Invoice - ' . $description));

// Remove default header and footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set margins
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, 15);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// add a page
$pdf->AddPage();

// Header Section with better spacing
$pdf->SetFillColor(248, 249, 250);
$pdf->Rect(0, 0, 210, 45, 'F');

// Company Logo and Info (Left side - This is the "Bill To" part, i.e. our business)
$pdf->SetXY(15, 20);
setKurdishFont($pdf, 'B', 16);
$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(0, 7, $_SESSION['business_name'], 0, 1, 'L');

$pdf->SetXY(15, 28);
setKurdishFont($pdf, '', 9);
$pdf->SetTextColor(108, 117, 125);
$pdf->Cell(0, 4, $order[0]['address'], 0, 1, 'L');

$pdf->SetXY(15, 32);
$pdf->Cell(0, 4, 'Phone: ' . $order[0]['contact'], 0, 1, 'L');

if (!empty($order[0]['warehouse_name'])) {
    $pdf->SetXY(15, 36);
    $pdf->Cell(0, 4, 'Warehouse: ' . $order[0]['warehouse_name'], 0, 1, 'L');
}

// Invoice Details (Right side)
$pdf->SetXY(120, 15);
setKurdishFont($pdf, 'B', 20);
$pdf->SetTextColor(52, 152, 219);
$pdf->Cell(0, 8, 'PURCHASE INVOICE', 0, 1, 'R');

$pdf->SetXY(120, 25);
setKurdishFont($pdf, 'B', 11);
$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(0, 5, '#PUR-' . str_pad($order[0]['id'], 6, '0', STR_PAD_LEFT), 0, 1, 'R');

$pdf->SetXY(120, 30);
setKurdishFont($pdf, '', 9);
$pdf->SetTextColor(108, 117, 125);
$pdf->Cell(0, 4, 'Date: ' . date_formats(strtotime($order[0]['created_at'])), 0, 1, 'R');

// Status Badge with better positioning
$status_y = 35;
if ($order[0]['payment_status'] == "fully_paid") {
    $status_color = array(40, 167, 69);
    $statustext = 'PAID';
} elseif ($order[0]['payment_status'] == "partially_paid") {
    $status_color = array(255, 193, 7);
    $statustext = 'PARTIAL';
} elseif ($order[0]['payment_status'] == "unpaid") {
    $status_color = array(52, 152, 219);
    $statustext = 'UNPAID';
} elseif ($order[0]['payment_status'] == "cancelled") {
    $status_color = array(220, 53, 69);
    $statustext = 'CANCELLED';
} else {
    $status_color = array(108, 117, 125);
    $statustext = 'PENDING';
}

$pdf->SetXY(120, $status_y);
$pdf->SetFillColor($status_color[0], $status_color[1], $status_color[2]);
$pdf->SetTextColor(255, 255, 255);
setKurdishFont($pdf, 'B', 8);
$pdf->Cell(30, 6, $statustext, 0, 0, 'C', true);

// Reset text color
$pdf->SetTextColor(33, 37, 41);

// Supplier Information Section
$pdf->SetY(55);
$pdf->SetFillColor(240, 242, 245);
$pdf->Rect(15, 55, 180, 30, 'F');

$pdf->SetXY(20, 60);
setKurdishFont($pdf, 'B', 11);
$pdf->Cell(0, 5, 'SUPPLIER:', 0, 1, 'L');

$pdf->SetXY(20, 66);
setKurdishFont($pdf, 'B', 10);
$pdf->Cell(0, 4, $order[0]['first_name'] . ' ' . (isset($order[0]['last_name']) ? $order[0]['last_name'] : ''), 0, 1, 'L');

$pdf->SetXY(20, 71);
setKurdishFont($pdf, '', 9);
$pdf->SetTextColor(108, 117, 125);
$pdf->Cell(0, 3, $order[0]['email'], 0, 1, 'L');

$pdf->SetXY(20, 75);
$pdf->Cell(0, 3, 'Phone: ' . $order[0]['mobile'], 0, 1, 'L');

// Items Table Header with better spacing
$pdf->SetY(95);
$pdf->SetFillColor(52, 152, 219);
$pdf->SetTextColor(255, 255, 255);
setKurdishFont($pdf, 'B', 9);

// Table headers with better column widths
$pdf->Cell(100, 7, 'Description', 0, 0, 'L', true);
$pdf->Cell(20, 7, 'Qty', 0, 0, 'C', true);
$pdf->Cell(30, 7, 'Price', 0, 0, 'R', true);
$pdf->Cell(30, 7, 'Total', 0, 1, 'R', true);

// Items Table Content with better formatting
$pdf->SetTextColor(33, 37, 41);
setKurdishFont($pdf, '', 8);
$y_position = 102;
$item_subtotal = 0;

foreach ($order as $item) :
    $item_name = $item['product_name'] . ' / ' . $item['variant_name'];
    $price = $item['price'];
    $quantity = $item['quantity'];
    $sub_total = $price * $quantity;
    $item_subtotal += $sub_total;

    // Alternate row colors for better readability
    if (($y_position - 102) % 14 == 0) {
        $pdf->SetFillColor(248, 249, 250);
    } else {
        $pdf->SetFillColor(255, 255, 255);
    }

    $pdf->SetXY(15, $y_position);
    
    // Truncate long descriptions properly
    $description = strlen($item_name) > 55 ? substr($item_name, 0, 52) . '...' : $item_name;
    $pdf->Cell(100, 7, $description, 0, 0, 'L', true);
    
    $pdf->Cell(20, 7, $quantity, 0, 0, 'C', true);
    $pdf->Cell(30, 7, currency_location(number_format($price, 2)), 0, 0, 'R', true);
    $pdf->Cell(30, 7, currency_location(number_format($sub_total, 2)), 0, 1, 'R', true);

    $y_position += 7;
endforeach;

// Summary Section with better positioning
$summary_y = $y_position + 15;
$pdf->SetFillColor(240, 242, 245);
$pdf->Rect(120, $summary_y, 75, 50, 'F');

$pdf->SetXY(125, $summary_y + 5);
setKurdishFont($pdf, 'B', 11);
$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(0, 5, 'SUMMARY', 0, 1, 'L');

// Subtotal
$pdf->SetXY(125, $summary_y + 15);
setKurdishFont($pdf, '', 9);
$pdf->Cell(40, 4, 'Subtotal:', 0, 0, 'L');
$pdf->Cell(30, 4, currency_location(number_format($item_subtotal, 2)), 0, 1, 'R');

// Delivery Charges
$pdf->SetXY(125, $summary_y + 20);
$delivery_charges = $order[0]['delivery_charges'] == 0 ? "N/A" : currency_location(decimal_points($order[0]['delivery_charges']));
$pdf->Cell(40, 4, 'Delivery:', 0, 0, 'L');
$pdf->Cell(30, 4, $delivery_charges, 0, 1, 'R');

// Discount
$pdf->SetXY(125, $summary_y + 25);
$discount = $order[0]['purchase_discount'] == 0 ? "N/A" : currency_location(decimal_points($order[0]['purchase_discount']));
$pdf->Cell(40, 4, 'Discount:', 0, 0, 'L');
$pdf->Cell(30, 4, $discount, 0, 1, 'R');

// Total with better emphasis
$pdf->SetXY(125, $summary_y + 35);
setKurdishFont($pdf, 'B', 11);
$pdf->SetTextColor(52, 152, 219);
$pdf->Cell(40, 5, 'TOTAL:', 0, 0, 'L');
$pdf->Cell(30, 5, currency_location(decimal_points($order[0]['total'])), 0, 1, 'R');

// Footer with better design
$pdf->SetY(-35);
$pdf->SetFillColor(248, 249, 250);
$pdf->Rect(0, $pdf->GetY(), 210, 35, 'F');

$pdf->SetXY(15, $pdf->GetY() + 5);
setKurdishFont($pdf, '', 8);
$pdf->SetTextColor(108, 117, 125);
$pdf->Cell(0, 3, 'Thank you for your business!', 0, 1, 'C');

$pdf->SetXY(15, $pdf->GetY() + 2);
$pdf->Cell(0, 3, 'Generated on ' . date('F j, Y \a\t g:i A'), 0, 1, 'C');

// Output PDF
$path = FCPATH . "public/invoice/purchase-";
$pdf->Output($path . $order[0]['id'] . '.pdf', 'F');
$myFile = $path . $order[0]['id'] . ".pdf";
if (file_exists($myFile)) {
    $pdf->Output('purchase-inv-' . $order[0]['id'] . '.pdf', 'D');
} 