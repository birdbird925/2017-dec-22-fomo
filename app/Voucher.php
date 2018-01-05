<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Voucher extends Model
{
    protected $table = 'voucher';
    protected $guarded = [];
    public $timestamps  = false;

    public function performance()
    {
        return $this->hasMany(VoucherHistory::class);
    }

    public function checkStatus()
    {
      if($this->status == 1) {
        $now = Carbon::now();
        $start_at = Carbon::parse($this->start_at);
        $expired_at = Carbon::parse($this->expired_at);

        if($now->lt($start_at))
          return -1;
        else if($expired_at->lt($now))
          return 0;
        else
          return 1;
      }
      else {
        return 0;
      }
    }
}
