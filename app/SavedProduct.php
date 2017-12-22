<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SavedProduct extends Model
{
    protected $guarded = [];
    public $timestamps  = false;
    protected $table = 'account_saved_products';

    public function product()
    {
        return $this->belongsTo(CustomizeProduct::class);
    }
}
