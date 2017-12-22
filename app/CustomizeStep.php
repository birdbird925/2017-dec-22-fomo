<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomizeStep extends Model
{
    protected $fillable = ['name'];
    public $timestamps  = false;
    protected $table = 'customize_steps';


    public function completeInfo()
    {
        return 'step='.$this->id.'
                direction='.$this->direction;
    }

    public function previousStep()
    {
        return CustomizeStep::find($this->id - 1) ? CustomizeStep::find($this->id - 1) : false;
    }

    public function nextStep()
    {
        return CustomizeStep::find($this->id + 1) ? CustomizeStep::find($this->id + 1) : false;
    }

    public function component()
    {
        return $this->hasMany(CustomizeComponent::class, 'step_id');
    }

    public function componentByLevel($level)
    {
        return $this->component()->where('level', $level)->orderBy('blank')->get();
    }

    public function personalizeOption()
    {
        return $this->component()->whereNotNull('personalize')->get();
    }

    public function extralOption()
    {
        return CustomizeComponentOption::whereIn('component_id', $this->component()->pluck('id'))
                                        ->get();
    }

    public function type()
    {
        return $this->belongsTo(CustomizeType::class, 'type_id');
    }
}
