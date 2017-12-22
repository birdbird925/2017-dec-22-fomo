<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomizeComponentOption extends Model
{
    protected $fillable = ['name'];
    public $timestamps  = false;
    protected $table = 'customize_component_options';

    public function radioAttr()
    {
        // target-element=.component'.$this->id.'
        return 'value='.$this->id.'
                desc-class=.extral
                extral-option=0
                personalize=0
                layer='.($this->layer ? $this->layer : 0).'
                front_image='.($this->front_image ? $this->image('front_image')->getSrc() : 0).'
                back_image='.($this->back_image ? $this->image('back_image')->getSrc() : 0);
    }

    public function image($column)
    {
        return $this->hasOne(Image::class, 'id', $column)->first();
    }

    public function component()
    {
        return $this->belongsTo(CustomizeComponent::class, 'component_id');
    }

}
