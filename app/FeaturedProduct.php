<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeaturedProduct extends Model
{
    protected $guarded = [];
    public $timestamps  = false;
    protected $table = 'cms_product';

    public function image()
    {
        return $this->belongsTo(Image::class, 'background');
    }

    public function product()
    {
        return $this->belongsTo(CustomizeProduct::class);
    }
}
