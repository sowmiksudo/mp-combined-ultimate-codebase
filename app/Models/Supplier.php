<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    public static $AMAZON = 1;
    public static $SHEIN = 2;
    public static $INHOUSE = 3;
}
