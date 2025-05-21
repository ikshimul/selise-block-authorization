<?php

namespace Inzamam\SeliseBlockAuthorization\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

if (Config::get('database.default') === 'mongodb') {
    class BlockAccessToken extends \MongoDB\Laravel\Eloquent\Model 
    {
        use HasFactory, SoftDeletes;
    }
} else {
    class BlockAccessToken extends \Illuminate\Database\Eloquent\Model 
    {
        use HasFactory, SoftDeletes;
    }
}