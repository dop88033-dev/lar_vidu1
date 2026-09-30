<?php

namespace App\Models;

/**
 * Product model mapping to categories table for compatibility
 */
class Product extends Category
{
    protected $table = 'categories';
}
