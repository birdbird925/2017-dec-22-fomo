<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\CustomizeComponent;
use App\CustomizeComponentOption;

class CustomizeProduct extends Model
{
    protected $guarded = [];
    public $timestamps  = false;
    protected $table = 'customize_products';

    public function owner()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function type()
    {
        return $this->belongsTo(CustomizeType::class, 'type_id');
    }

    public function checkComponentStatus()
    {
        $images = json_decode($this->images);
        $radioInput = json_decode($this->components);
        $sizeComponentID = 0;
        $personalizeImage = false;
        $components = [];


        foreach($radioInput as $name=>$input) {
            if($name == 'customize_type') {
                array_push($components, $this->type);
            }
            // if input was extral radio button
            else if(substr($name, -6) == 'extral') {
                $component = CustomizeComponentOption::find($input->value);
                if($component) array_push($components, $component);
            }
            // input is personalize item
            else if (strpos($name, 'personalize') !== false){
                if(isset($input->rotation))
                    $personalizeImage = true;
            }
            // if input was main radio button
            else {
                $component = CustomizeComponent::find($input->value);
                if($component) {
                    // check is size component
                    if($component->size_component) $sizeComponentID = $input->value;
                    array_push($components, $component);
                }
            }
        }

        foreach($components as $component) {
            if($sizeComponentID != 0 && $component->size_image) {
                $sizeImage = json_decode($component->size_image);

                if(isset($sizeImage->$sizeComponentID->front_image)) {
                    $frontImage = $sizeImage->$sizeComponentID->front_image;
                    if (($key = array_search(Image::find($frontImage)->getSrc(), $images)) !== false)
                        unset($images[$key]);
                }

                if(isset($sizeImage->$sizeComponentID->back_image)) {
                    $backImage = $sizeImage->$sizeComponentID->back_image;
                    if (($key = array_search(Image::find($backImage)->getSrc(), $images)) !== false)
                        unset($images[$key]);
                }

            }
            else {
                if($component->front_image)
                    if (($key = array_search($component->image('front_image')->getSrc(), $images)) !== false)
                        unset($images[$key]);

                if($component->back_image)
                    if (($key = array_search($component->image('back_image')->getSrc(), $images)) !== false)
                        unset($images[$key]);
            }

        }

        return ($personalizeImage ? sizeof($images) == 1 : sizeof($images) == 0);
    }
}
