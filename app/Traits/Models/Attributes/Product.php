<?php

namespace App\Traits\Models\Attributes;

use App\Services\CurrencyService;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait Product
{
    protected function priceEur(): Attribute
    {
        return Attribute::make(
            get: fn() => (new CurrencyService())->convert($this->price, 'usd', 'eur'),
        );
    }
}
