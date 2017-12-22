<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $guarded = [];
    public $timestamps  = false;
    protected $table = 'cms_slider';

    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}
