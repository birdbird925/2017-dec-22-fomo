<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VoucherHistory extends Model
{
    protected $table = 'voucher_history';
    protected $guarded = [];
    public $timestamps  = false;
}
