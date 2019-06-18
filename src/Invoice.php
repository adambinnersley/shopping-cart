<?php

namespace ShoppingCart;

use DBAL\Database;
use Configuration\Config;
use ShoppingCart\FPDF\FPDF;
use ShoppingCart\Modifiers\Cost;

/**
 * @codeCoverageIgnore
 */
class Invoice {
    protected $db;
    protected $config;
    protected $pdf;
    protected $order;
    
    public $decimals;
    public $symbol;

    /**
     * Constructor
     * @param Database $db This need to be an instance of the database class
     * @param Config $config This should be an instance of the config class
     * @param Order $order This should be an instance of the order class
     */
    public function __construct(Database $db, $config, $order) {
        $this->db = $db;
        $this->config = $config;
        $this->order = $order;
        $this->decimals = Currency::getCurrencyDecimals($this->config->currency);
        $this->symbol = Currency::getCurrencySymbol($this->config->currency);
        $this->pdf = new FPDF();
    }
    
    /**
     * Creates the PDF document headers
     */
    protected function pdfHeaders() {
        $this->pdf->SetTitle("{$this->config->site_name} - Order Invoice");
        $this->pdf->SetSubject("Order Invoice");
        $this->pdf->SetAuthor("{$this->config->site_name} [{$this->config->site_url}]");
        $this->pdf->SetCreator($this->config->site_name);
    }
    
    /**
     * Outputs the order total information to the PDF
     * @param array $orderInfo This should be an array containing all of the order information 
     */
    protected function cartTotals($orderInfo) {
        $this->pdf->Cell(94, 7, '', 0, 1);
        
        $this->pdf->Cell(129, 7, '');
        $this->pdf->Cell(30, 7, 'Sub Total', 1, 0, 'R');
        $this->pdf->Cell(30, 7, utf8_decode($this->symbol).$orderInfo['subtotal'], 1, 1, 'R');
        $this->pdf->Cell(129, 7, '');
        $this->pdf->Cell(30, 7, 'VAT', 1, 0, 'R');
        $this->pdf->Cell(30, 7, utf8_decode($this->symbol).$orderInfo['total_tax'], 1, 1, 'R');
        
        if(Cost::priceUnits($orderInfo['discount'], $this->decimals) !== Cost::priceUnits(0, $this->decimals)) {
            $this->pdf->Cell(129, 7, '');
            $this->pdf->Cell(30, 7, 'Discount', 1, 0, 'R');
            $this->pdf->Cell(30, 7, utf8_decode($this->symbol).$orderInfo['discount'], 1, 1, 'R');
        }
        $this->pdf->Cell(129, 7, '');
        $this->pdf->Cell(30, 7, 'Delivery', 1, 0, 'R');
        $this->pdf->Cell(30, 7, utf8_decode($this->symbol).$orderInfo['delivery'], 1, 1, 'R');
        $this->pdf->Cell(129, 7, '');
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(30, 7, 'Total', 1, 0, 'R', true);
        $this->pdf->Cell(30, 7, utf8_decode($this->symbol).$orderInfo['cart_total'], 1, 1, 'R', true);
        $this->pdf->SetFont('Arial', '', 10);
    }
    
    /**
     * Creates a PDF invoice for the given order
     * @param int $orderNo This should be the unique order_no
     * @param int $userID This should be the current users ID
     * @param boolean $download If you want the file to force a download instead of displaying in-line set to true 
     * @param boolean $print
     * @param boolean $send
     */
    public function createInvoice($orderNo, $userID, $download = false, $print = false, $send = false) {
        $this->pdfHeaders();
        
        $orderInfo = $this->order->getOrderByID($orderNo);
        if(is_array($orderInfo) && is_array($orderInfo['products']) && intval($orderInfo['customer_id']) === intval($userID)){        
            $this->pdf->AddPage();
            $this->pdf->SetAutoPageBreak(true, 0);
            $this->pdf->AliasNbPages();
            $this->pdf->SetDrawColor(168,168,168);
            $this->pdf->SetFillColor(241,241,241);
            $this->pdf->SetTextColor(54,54,54);

            if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->config->logo_root_path)) {
                $this->pdf->Image($_SERVER['DOCUMENT_ROOT'].$this->config->logo_root_path, 10, 10, 40, 20);
            }
            $this->pdf->SetFont('Arial', 'B', 18);
            $this->pdf->Cell(189, 5, 'Invoice / Receipt', 0, 1, 'R');
            $this->pdf->Cell(189, 25, '', 0, 1);
            $this->pdf->SetFont('Arial', '', 10);

            $this->pdf->Cell(94, 5, '');
            $this->pdf->Cell(40, 5, 'Invoice Number', 1, 0, 'R');
            $this->pdf->Cell(55, 5, $this->createInvoiceID($orderInfo['order_id']), 1, 1, 'R');

            $this->pdf->Cell(94, 5, '');
            $this->pdf->Cell(40, 5, 'Order Number', 1, 0, 'R');
            $this->pdf->Cell(55, 5, $orderInfo['order_no'], 1, 1, 'R');

            $this->pdf->SetFont('Arial', 'B', 12);
            $this->pdf->Cell(94, 5, 'From:');
            $this->pdf->SetFont('Arial', '', 10);
            $this->pdf->Cell(40, 5, 'Order Date', 1, 0, 'R');
            $this->pdf->Cell(55, 5, date('jS F Y', strtotime(($orderInfo['payment_date'] != NULL ? $orderInfo['payment_date'] : 'now'))), 1, 1, 'R');

            $this->pdf->SetTextColor(51,153,204);
            $this->pdf->Cell(94, 4, $this->config->site_name, 0, 0, '', false, $this->config->site_url);
            $this->pdf->SetTextColor(54,54,54);
            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->Cell(40, 5, 'Order Total', 1, 0, 'R', true);
            $this->pdf->Cell(55, 5, utf8_decode($this->symbol).$orderInfo['cart_total'], 1, 1, 'R', true);
            $this->pdf->SetFont('Arial', '', 10);

            $this->pdf->MultiCell(90, 4, str_replace([', ', ' ,'], "\n\r", $this->config->registered_address));
            $this->pdf->Cell(189, 10, '', 0, 1);

            $this->pdf->SetFont('Arial', 'B', 12);
            $this->pdf->Cell(94, 6, 'To:', 0, 1);
            $this->pdf->SetFont('Arial', '', 10);
            $this->pdf->Cell(95, 4, $orderInfo['delivery_info']['title'].' '.$orderInfo['delivery_info']['firstname'].' '.$orderInfo['delivery_info']['lastname'], 0, 1);
            $this->pdf->Cell(120, 4, $orderInfo['delivery_info']['add_1'], 0, 1);
            if(!empty(trim($orderInfo['delivery_info']['add_2']))) {$this->pdf->Cell(120, 4, $orderInfo['delivery_info']['add_2'], 0, 1);}
            $this->pdf->Cell(120, 4, $orderInfo['delivery_info']['town'], 0, 1);
            $this->pdf->Cell(120, 4, $orderInfo['delivery_info']['county'], 0, 1);
            $this->pdf->Cell(120, 4, $orderInfo['delivery_info']['postcode'], 0, 1);

            $this->pdf->Cell(189, 10, '', 0, 1);

            $this->listItems($orderInfo['products']);
            $this->cartTotals($orderInfo);

            $this->pdf->Cell(55, 15, '', 0, 1);
            $this->pdf->SetFont('Arial', 'B', 16);
            $this->pdf->Cell(189, 7, 'Thanks for choosing '.$this->config->site_name, 0, 1, 'C');
            $this->pdf->SetFont('Arial', 'B', 12);
            $this->pdf->Cell(189, 7, $this->config->site_url, 0, 1, 'C', false, $this->config->site_url);
            $this->pdf->SetFont('Arial', '', 10);

            $this->footer();
            if($print === true){$this->pdf->AutoPrint(true);}
            return $this->pdf->Output(($download === true ? 'D' : ($send === true ? 'S' : 'I')), 'Order'.$orderInfo['order_no'].'.pdf');
        }
        else{
            echo('Document not found');
        }
    }
    
    /**
     * Creates a table to output to the PDF
     * @param array $header This should be an array containing the table headers
     * @param array $data This should be an array containing the table contents
     * @param array $widths This should be an array containing the with of the table fields
     * @param int $height The height of the table rows
     * @param array $left If the records should be left aligned set to true else set to false to centre align table elements
     */
    protected function pdfTable($header, $data, $widths, $height = 6, $left = false) {
        $this->pdf->SetFont('Arial', 'B');
        foreach($header as $i => $col) {
            $this->pdf->Cell($widths[$i], 7, $col, 1, 0, 'C', true);
        }
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '');
        foreach($data as $row) {
            $bottom = (isset($row['download']) && $row['download'] !== true ? 'B' : '');
            unset($row['download']);
            foreach($row as $i => $col) {
                if (is_array($col)){$this->pdf->SetFont('Arial', 'B', 9);$this->pdf->SetTextColor(51,153,204);}
                $this->pdf->Cell($widths[$i], $height, (is_array($col) ? $col[0] : $col), 'LR'.$bottom, 0, ($left[$i] !== false ? 'L' : 'C'), false, (is_array($col) ? $col[1] : false));
                if (is_array($col)){$this->pdf->SetFont('Arial', '', 10);$this->pdf->SetTextColor(54,54,54);}
            }
            $this->pdf->Ln();
        }
    }
    
    
    
    /**
     * Creates a list of the items in the order to display on the PDF
     * @param array $products This should be an array containing the products purchased
     */
    protected function listItems($products) {
        $productArray = [];
        $i = 0;
        foreach($products as $productDetails) {
            $productArray[$i][0] = $productDetails['quantity'];
            $productArray[$i][1] = $productDetails['name'];
            $productArray[$i][2] = utf8_decode($this->symbol).Cost::priceUnits(($productDetails['price'] - $productDetails['tax']), $this->decimals);
            $productArray[$i][3] = utf8_decode($this->symbol).Cost::priceUnits($productDetails['tax'], $this->decimals);
            $productArray[$i][4] = utf8_decode($this->symbol).Cost::priceUnits(($productDetails['quantity'] * $productDetails['price']), $this->decimals);
            if(isset($productDetails['dlid'])){$productArray[$i]['download'] = true;}
            $i++;
            if(isset($productDetails['dlid'])){
                $productArray[$i] = ["", ['Download Link', $productDetails['link']], "", ""];
                $i++;
                if(is_array($productDetails['serials'])){
                    foreach($productDetails['serials'] as $serials){
                        $productArray[$i] = ["", "Serial: ".$serials['serial'], "", ""];
                        $i++;
                    }
                }
            }
            
        }
        $this->pdfTable(
            ['Qty', 'Product', 'Unit Price', 'Unit Tax', 'Total'],
            $productArray,
            [12, 87, 30, 30, 30],
            7,
            [false, true, false, false, false]
        );
    }
    
    /**
     * Creates a unique Invoice ID to output to the PDF
     * @param string $id This can be the unique order number or any unique number to give to the invoice
     * @return string The invoice ID will be returned
     */
    protected function createInvoiceID($id) {
        $words = explode(" ", $this->config->site_name);
        $acronym = "";

        foreach ($words as $w) {
          $acronym .= $w[0];
        }
        return $acronym.$id;
    }
    
    /**
     * Adds a static footer to all of the PDF pages 
     */
    protected function footer() {
        $this->pdf->SetY(-20, true);
        $this->pdf->SetFont('Arial','I',7);
        $this->pdf->MultiCell(189, 5, 'Registered as '.($this->config->trading_as ? $this->config->trading_as.', ' : '').$this->config->registered_address.'. '.($this->config->vat_number ? 'VAT No. '.$this->config->vat_number : ''), 0, 'C');
        $this->pdf->Cell(189, 5, 'Page '.$this->pdf->PageNo().' of {nb}', 0, 1, 'C');
    }
}


