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

    public function generate($userID, $discount, $validity, $delivery = null)
    {
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $code = "";
        do {
            for ($i = 0; $i < 10; $i++) {
                $code .= $chars[mt_rand(0, strlen($chars)-1)];
            }
        } while(Voucher::where('code', $code)->count() != 0);

        $this->code = $code;
        $this->user_id = $userID;
        $this->discount = $discount;
        $this->expired_at = Carbon::now()->addDays($validity)->startOfDay();
        $this->free_delivery = $delivery != null ? 1 : 0 ;
    }
}
