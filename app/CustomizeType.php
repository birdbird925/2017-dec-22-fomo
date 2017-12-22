<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomizeType extends Model
{
    protected $guarded = [];
    public $timestamps  = false;
    protected $table = 'customize_types';

    public function radioAttr()
    {
        return 'value='.$this->id.'
                desc-class=.description
                hide-class=.customize-element
                show-class=.customize'.$this->id.'
                layer=0
                front_image='.($this->front_image ? $this->image('front_image')->getSrc() : 0).'
                back_image='.($this->back_image ? $this->image('back_image')->getSrc() : 0).'
                front_personalize='.($this->front_personalize ? $this->image('front_personalize')->getSrc() : 0).'
                back_personalize='.($this->back_personalize ? $this->image('back_personalize')->getSrc() : 0);
    }

    public function image($column)
    {
        return $this->hasOne(Image::class, 'id', $column)->first();
    }

    public function component()
    {
        return $this->hasMany(CustomizeComponent::class, 'type_id');
    }
}
