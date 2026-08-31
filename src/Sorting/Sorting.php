<?php

namespace App\Sorting;

class Sorting
{
    const DISPATCH_SEPARATOR = ':';

    public function __construct(
        public readonly string $field, 
        public readonly string $name,
        public readonly array $customNames = []
    ) {
        
    }

    public function getAlias() : string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $this->name);
    }

    public function getVariants(array $orders) : array
    {
        $variants = [];

        foreach ($orders as $order) {
            $dispatch = self::buildDispatch($this->getAlias(), $order);
            $variants[$dispatch] = [
                'dispatch' => $dispatch,
                'name' => $this->customNames[$order] ?? self::buildDefaultName($this->name, $order)
            ];
        }

        return $variants;
    }

    public static function buildDispatch($alias, $order) 
    {
        return $alias . self::DISPATCH_SEPARATOR . $order;
    }

    public static function parseDispatch(string $dispatch) : array
    {
        return explode(self::DISPATCH_SEPARATOR, $dispatch);
    }

    public static function buildDefaultName($name, $order)
    {
        $name = str_replace('_', ' ', $name);

        return $name . ' (' . strtolower($order) . ')';
    }
}
