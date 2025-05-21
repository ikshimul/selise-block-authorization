<?php

namespace Inzamam\SeliseBlockAuthorization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

if (Config::get('database.default') === 'mongodb') {
    class BlockApiLog extends \MongoDB\Laravel\Eloquent\Model
    {
        protected $guarded = ['id'];
    }
} else {
    class BlockApiLog extends Model
    {
        protected $guarded = ['id'];
    }
}