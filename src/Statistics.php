<?php

namespace ShoppingCart;

use DateTime;
use DBAL\Database;
use Configuration\Config;
use ShoppingCart\Modifiers\Cost;

class Statistics
{
    protected $db;
    public $config;
    
    public $decimals;

    /**
     * Constructor
     * @param Database $db This should be an instance of the database class
     * @param Config $config This should be the ShoppingCart\Config class instance
     */
    public function __construct(Database $db, Config $config)
    {
        $this->db = $db;
        $this->config = $config;
        $this->decimals = Currency::getCurrencyDecimals($this->config->currency);
    }
    
    /**
     * Gets the daily sales for a given month and year
     * @param int $month This is the month that you wish to get the statistics for
     * @param int $year This is the year that you wish to get the statistics for
     * @param array $additionalInfo Any additional fields to limit the queries with
     * @return array And array of the daily sales for the given month will be returned
     */
    public function getSalesByMonth($month, $year, $additionalInfo = [])
    {
        $daysales = [];
        for ($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++) {
            $totalsales = 0;
            $sales = $this->db->selectAll($this->config->table_basket, array_merge(['status' => ['IN' => [2, 3]], 'date' => ['BETWEEN' => [$year . "-" . sprintf("%02d", $month) . "-" . sprintf("%02d", $day) . " 00:00:00", $year . "-" . sprintf("%02d", $month) . "-" . sprintf("%02d", $day) . " 23:59:59"]]], $additionalInfo), '*', [], 0, 3600);
            if (is_array($sales)) {
                foreach ($sales as $totals) {
                    $totalsales = $totalsales + $totals['cart_total'];
                }
            }
            $daysales['days'][] = $day;
            $daysales['statistics'][] = Cost::priceUnits($totalsales, $this->decimals);
        }
        return $daysales;
    }
    
    /**
     * Gets the years sales by month
     * @param int $year This is the year that you wish to get the statistics for
     * @param array $additionalInfo Any additional fields to limit the queries with
     * @return array And array of the monthly sales for the given year will be returned
     */
    public function getSalesByYear($year, $additionalInfo = [])
    {
        $monthsales = [];
        for ($month = 1; $month <= 12; $month++) {
            $totalsales = 0;
            $sales = $this->db->selectAll($this->config->table_basket, array_merge(['status' => ['IN' => [2, 3]], 'date' => ['BETWEEN' => [$year . "-" . sprintf("%02d", $month) . "-01 00:00:00", $year . "-" . sprintf("%02d", $month) . "-31 23:59:59"]]], $additionalInfo), '*', [], 0, 3600);
            if (is_array($sales)) {
                foreach ($sales as $totals) {
                    $totalsales = $totalsales + $totals['cart_total'];
                }
            }
            $dateObj = DateTime::createFromFormat('!m', $month);
            $monthsales['months'][] = $dateObj->format('F');
            $monthsales['statistics'][] = Cost::priceUnits($totalsales, $this->decimals);
        }
        return $monthsales;
    }
    
    /**
     * Returns the statistics by total number of sales per product
     * @param array $where Parameters to search on
     * @return array An array containing the number of sales and percentage of total sales will be returned
     */
    public function getProductStatsBySales($where = [])
    {
        return $this->getProductStats('sales', $where);
    }
    
    /**
     * Returns the statistics by total number of views per product
     * @param array $where Parameters to search on
     * @return array An array containing the number of views and percentage of total views will be returned
     */
    public function getProductStatsByViews($where = [])
    {
        return $this->getProductStats('views', $where);
    }
    
    /**
     * Returns the statistics for the products based on the parameter given
     * @param string $order_by This should be the fields that you want to order the statistics on
     * @return array An array containing the total number and percentages will be returned
     */
    protected function getProductStats($order_by = 'sales', $where = [])
    {
        $productList = $this->db->selectAll($this->config->table_products, array_merge(['active' => 1], $where), '*', [$order_by => 'DESC'], 0, 3600);
        $total = 0;
        $stats = [];
        foreach ($productList as $product) {
            $total = $total + $product[$order_by];
        }
        foreach ($productList as $i => $product) {
            $stats[$i][$order_by] = $product[$order_by];
            $stats[$i]['percentage'] = number_format(($product[$order_by] / $total) * 100, 2, '.', '');
        }
        return $stats;
    }
    
    /**
     * Gets the daily sales for a given product for a selected month and year
     * @param int $product_id This is the unique product id that you want to get the statistics for
     * @param int $month This is the month that you wish to get the statistics for
     * @param int $year This is the year that you wish to get the statistics for
     * @return array An array containing the sales for each day in the given month will be returned
     */
    public function getProductSalesByMonth($product_id, $month, $year)
    {
        $daysales = [];
        for ($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++) {
            $sales = $this->db->query("SELECT SUM(`products`.`quantity`) as `sales` FROM `{$this->config->table_basket}` as `basket`, `{$this->config->table_basket_products}` as `products` WHERE `basket`.`status` IN (2, 3) AND `basket`.`order_id` = `products`.`order_id` AND `products`.`product_id` = ? AND `basket`.`date` BETWEEN ? AND ?;", [$product_id, $year . "-" . sprintf("%02d", $month) . "-" . sprintf("%02d", $day) . " 00:00:00", $year . "-" . sprintf("%02d", $month) . "-" . sprintf("%02d", $day) . " 23:59:59"], 3600);
            $daysales['days'][] = $day;
            $daysales['statistics'][] = $sales[0]['sales'];
        }
        return $daysales;
    }
    
    /**
     * Gets the monthly sales for a given product for a selected year
     * @param int $product_id This is the unique product id that you want to get the statistics for
     * @param int $year This is the year that you wish to get the statistics for
     * @return array An array containing the sales for each month in the given year will be returned
     */
    public function getProductSalesByYear($product_id, $year)
    {
        $monthsales = [];
        for ($month = 1; $month <= 12; $month++) {
            $sales = $this->db->query("SELECT SUM(`products`.`quantity`) as `sales` FROM `{$this->config->table_basket}` as `basket`, `{$this->config->table_basket_products}` as `products` WHERE `basket`.`status` IN (2, 3) AND `basket`.`order_id` = `products`.`order_id` AND `products`.`product_id` = ? AND `basket`.`date` BETWEEN ? AND ?;", [$product_id, $year . "-" . sprintf("%02d", $month) . "-01 00:00:00", $year . "-" . sprintf("%02d", $month) . "-31 23:59:59"], 3600);
            $dateObj = DateTime::createFromFormat('!m', $month);
            $monthsales['months'][] = $dateObj->format('F');
            $monthsales['statistics'][] = $sales[0]['sales'];
        }
        return $monthsales;
    }
}
