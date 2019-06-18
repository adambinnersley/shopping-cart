<?php

namespace ShoppingCart\Modifiers;

class Cost {
    /**
     * Returns the correct pricing unit with correct decimals depending on currency settings
     * @param string $price This should be the price of the item
     * @param int $decimals This should be th number of decimals
     * @return string Returns the formated price string
     */
    public static function priceUnits($price, $decimals) {
        return number_format($price, intval($decimals), '.', '');
    }
}