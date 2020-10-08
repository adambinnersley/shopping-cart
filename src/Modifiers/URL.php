<?php

namespace ShoppingCart\Modifiers;

class URL
{
    public static function makeURI($string)
    {
        return preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $string)));
    }
}
