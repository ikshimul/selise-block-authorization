<?php

namespace Inzamam\SeliseBlockAuthorization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockAccessToken extends Model
{
    use HasFactory, SoftDeletes;
}