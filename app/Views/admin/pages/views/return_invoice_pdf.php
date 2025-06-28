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

$description = $return_data['product_name'] . ' - ' . $return_data['variant_name'];

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetTitle(labels('return_invoice_label', 'Return Invoice - ' . $description));

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
$pdf->Cell(0, 4, $business_address ?? 'Business Address', 0, 1, 'L');

$pdf->SetXY(15, 32);
$pdf->Cell(0, 4, 'Phone: ' . ($business_contact ?? 'Business Contact'), 0, 1, 'L');

if (!empty($warehouse_name)) {
    $pdf->SetXY(15, 36);
    $pdf->Cell(0, 4, 'Warehouse: ' . $warehouse_name, 0, 1, 'L');
}

// Invoice Details (Right side)
$pdf->SetXY(120, 15);
setKurdishFont($pdf, 'B', 20);
$pdf->SetTextColor(220, 53, 69); // Red color for returns
$pdf->Cell(0, 8, 'RETURN INVOICE', 0, 1, 'R');

$pdf->SetXY(120, 25);
setKurdishFont($pdf, 'B', 11);
$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(0, 5, '#RET-' . str_pad($return_id, 6, '0', STR_PAD_LEFT), 0, 1, 'R');

$pdf->SetXY(120, 30);
setKurdishFont($pdf, '', 9);
$pdf->SetTextColor(108, 117, 125);
$pdf->Cell(0, 4, 'Date: ' . date('Y-m-d', strtotime($return_date)), 0, 1, 'R');

// Status Badge with better positioning
$status_y = 35;
$status_color = array(220, 53, 69); // Red for returns
$statustext = 'RETURNED';

$pdf->SetXY(120, $status_y);
$pdf->SetFillColor($status_color[0], $status_color[1], $status_color[2]);
$pdf->SetTextColor(255, 255, 255);
setKurdishFont($pdf, 'B', 8);
$pdf->Cell(30, 6, $statustext, 0, 0, 'C', true);

// Reset text color
$pdf->SetTextColor(33, 37, 41);

// Original Purchase Information Section
$pdf->SetY(55);
$pdf->SetFillColor(240, 242, 245);
$pdf->Rect(15, 55, 180, 30, 'F');

$pdf->SetXY(20, 60);
setKurdishFont($pdf, 'B', 11);
$pdf->Cell(0, 5, 'ORIGINAL PURCHASE:', 0, 1, 'L');

$pdf->SetXY(20, 66);
setKurdishFont($pdf, 'B', 10);
$pdf->Cell(0, 4, 'Purchase #PUR-' . str_pad($purchase_id, 6, '0', STR_PAD_LEFT), 0, 1, 'L');

$pdf->SetXY(20, 71);
setKurdishFont($pdf, '', 9);
$pdf->SetTextColor(108, 117, 125);
$pdf->Cell(0, 3, 'Supplier: ' . $supplier_name, 0, 1, 'L');

$pdf->SetXY(20, 75);
$pdf->Cell(0, 3, 'Original Purchase Date: ' . date('Y-m-d', strtotime($original_purchase_date)), 0, 1, 'L');

// Return Reason Section
$pdf->SetY(95);
$pdf->SetFillColor(255, 248, 248); // Light red background
$pdf->Rect(15, 95, 180, 20, 'F');

$pdf->SetXY(20, 100);
setKurdishFont($pdf, 'B', 11);
$pdf->SetTextColor(220, 53, 69);
$pdf->Cell(0, 5, 'RETURN REASON:', 0, 1, 'L');

$pdf->SetXY(20, 106);
setKurdishFont($pdf, '', 9);
$pdf->SetTextColor(108, 117, 125);
$pdf->MultiCell(170, 4, $return_reason, 0, 'L');

// Items Table Header with better spacing
$pdf->SetY(125);
$pdf->SetFillColor(220, 53, 69); // Red header for returns
$pdf->SetTextColor(255, 255, 255);
setKurdishFont($pdf, 'B', 9);

// Table headers with better column widths
$pdf->Cell(100, 7, 'Description', 0, 0, 'L', true);
$pdf->Cell(20, 7, 'Qty', 0, 0, 'C', true);
$pdf->Cell(30, 7, 'Return Price', 0, 0, 'R', true);
$pdf->Cell(30, 7, 'Total', 0, 1, 'R', true);

// Items Table Content with better formatting
$pdf->SetTextColor(33, 37, 41);
setKurdishFont($pdf, '', 8);
$y_position = 132;

// Return item row
$pdf->SetFillColor(255, 248, 248); // Light red background for return item

$pdf->SetXY(15, $y_position);
$item_name = $return_data['product_name'] . ' / ' . $return_data['variant_name'];
$description = strlen($item_name) > 55 ? substr($item_name, 0, 52) . '...' : $item_name;
$pdf->Cell(100, 7, $description, 0, 0, 'L', true);

$pdf->Cell(20, 7, $return_quantity, 0, 0, 'C', true);
$pdf->Cell(30, 7, currency_location(number_format($return_price, 2)), 0, 0, 'R', true);
$pdf->Cell(30, 7, currency_location(number_format($return_total, 2)), 0, 1, 'R', true);

// Summary Section with better positioning
$summary_y = $y_position + 15;
$pdf->SetFillColor(240, 242, 245);
$pdf->Rect(120, $summary_y, 75, 50, 'F');

$pdf->SetXY(125, $summary_y + 5);
setKurdishFont($pdf, 'B', 11);
$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(0, 5, 'SUMMARY', 0, 1, 'L');

// Items Count
$pdf->SetXY(125, $summary_y + 15);
setKurdishFont($pdf, '', 9);
$pdf->Cell(40, 4, 'Items Returned:', 0, 0, 'L');
$pdf->Cell(30, 4, $return_quantity, 0, 1, 'R');

// Return Total with better emphasis
$pdf->SetXY(125, $summary_y + 25);
setKurdishFont($pdf, 'B', 11);
$pdf->SetTextColor(220, 53, 69); // Red color for return total
$pdf->Cell(40, 5, 'RETURN TOTAL:', 0, 0, 'L');
$pdf->Cell(30, 5, currency_location(number_format($return_total, 2)), 0, 1, 'R');

// Footer with better design
$pdf->SetY(-35);
$pdf->SetFillColor(248, 249, 250);
$pdf->Rect(0, $pdf->GetY(), 210, 35, 'F');

$pdf->SetXY(15, $pdf->GetY() + 5);
setKurdishFont($pdf, '', 8);
$pdf->SetTextColor(108, 117, 125);
$pdf->Cell(0, 3, 'Generated on ' . date('F j, Y \a\t g:i A'), 0, 1, 'C');

// Output PDF
$path = FCPATH . "public/invoice/return-";
$pdf->Output($path . $return_id . '.pdf', 'F');
$myFile = $path . $return_id . ".pdf";
if (file_exists($myFile)) {
    $pdf->Output('return-inv-' . $return_id . '.pdf', 'D');
} 