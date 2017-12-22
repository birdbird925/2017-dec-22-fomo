<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
      'name', 'path', 'alt', 'isProductImage'
    ];
    public $timestamps  = false;
    protected $table = 'images';

    public static $rules = [
        'file' => 'required|image'
    ];

    public static $messages = [
        'file.image' => 'is not in image format',
        'file.required' => 'Image is required'
    ];

    public function getSrc()
    {
        return '/'.$this->path.$this->name;
    }
}
