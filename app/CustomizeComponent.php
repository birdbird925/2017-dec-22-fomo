<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomizeComponent extends Model
{
    protected $fillable = ['name'];
    public $timestamps  = false;
    protected $table = 'customize_components';

    public function radioAttr($step)
    {
        return 'value='.$this->id.'
                desc-class=.main
                hide-class=.extral'.$step.'
                show-class=.component'.$this->id.'
                extral-option='.($this->option->count() > 0 ? 1 : 0).'
                personalize='.($this->personalize ? $this->personalize : 0).'
                hide-personalize=.'.($this->personalize ? 'step'.$step.'personalize' : 0).'
                show-personalize=.'.($this->personalize ? 'step'.$step.'personalize'.$this->id : 0).'
                size-component='.($this->size_component ? $this->size_component : 0).'
                layer='.($this->layer ? $this->layer : 0).'
                front_image='.($this->front_image ? $this->image('front_image')->getSrc() : 0).'
                back_image='.($this->back_image ? $this->image('back_image')->getSrc() : 0);
    }

    public function image($column)
    {
        return $this->hasOne(Image::class, 'id', $column)->first();
    }

    public function option()
    {
        return $this->hasMany(CustomizeComponentOption::class, 'component_id');
    }

    public function step()
    {
        return $this->belongsTo(CustomizeStep::class);
    }

    public function customizeType()
    {
        return $this->belongsTo(CustomizeType::class, 'type_id');
    }

    public function checkType($id)
    {
        if($this->type_id)
            return $this->type_id == $id;
        else
            return true;
    }
}
