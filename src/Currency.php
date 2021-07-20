<?php

namespace ShoppingCart;

class Currency
{
    
    /**
     * This should be empty until the array of currencies is set
     * @var * If currencies list is retrieved will be set as an array else will be empty
     */
    public static $currencies;

    /**
     * Retrieves a list of the currencies
     */
    private static function retrieveCurrencies()
    {
        if (empty(self::$currencies) || !is_array(self::$currencies)) {
            $file = file_get_contents('Currencies/Common-Currency.json', true);
            self::$currencies = json_decode($file, true);
        }
    }
    
    /**
     * Gets the currency symbol by code
     * @param string $code This should be the unique currency code
     * @return string Returns the symbol if the currency code exists else returns false
     */
    public static function getCurrencySymbol($code)
    {
        self::retrieveCurrencies();
        return isset(self::$currencies[strtoupper($code)]) ? self::$currencies[strtoupper($code)]['symbol'] : false;
    }
    
    /**
     * Gets the currency name by code
     * @param string $code This should be the unique currency code
     * @return string Returns the currency name if the currency code exists else return false
     */
    public static function getCurrencyName($code)
    {
        self::retrieveCurrencies();
        return isset(self::$currencies[strtoupper($code)]) ? self::$currencies[strtoupper($code)]['name'] : false;
    }
    
    /**
     * Gets the number of decimals for the given currency
     * @param string $code This should be the unique currency code
     * @return int Returns the number of decimals if the currency code exists else returns false
     */
    public static function getCurrencyDecimals($code)
    {
        self::retrieveCurrencies();
        return isset(self::$currencies[strtoupper($code)]) ? intval(self::$currencies[strtoupper($code)]['decimal_digits']) : 2;
    }
    
    /**
     * Produces a list of all of the currency names
     * @return array Will return a list of all of the currency names existing in the file
     */
    public static function listCurrencyNames()
    {
        $names = [];
        self::retrieveCurrencies();
        foreach (self::$currencies as $currency) {
            $names[] = $currency['name'];
        }
        return $names;
    }
    
    /**
     * Produces a list of all of the currency names
     * @return array Will return a list of all of the currency names existing in the file
     */
    public static function listCurrencyCodes()
    {
        $codes = array_keys(self::$currencies);
        sort($codes);
        return $codes;
    }
}
