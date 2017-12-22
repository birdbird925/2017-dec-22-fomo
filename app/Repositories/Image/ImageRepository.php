<?php

namespace App\Repositories\Image;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use App\Image;

class ImageRepository
{
    public function upload($imageData)
    {
        $validator = Validator::make($imageData, Image::$rules, Image::$messages);

        if($validator->fails())
          return Response::json([
            'error' => true,
            'message' => $validator->messages()->first(),
            'code' => 400
          ], 400);

        $path = $imageData['isProductImage'] ? 'images/product/' : 'images/';
        $name = $imageData['file']->hashName();
        $imagePath = $imageData['file']->store($path);

        // insert into database
        $image = new Image([
          'name' => $name,
          'path' => $path,
          'isProductImage' => request('isProductImage', 0)
        ]);

        $image->save();

        // generate 2 different size as medium, thumbnail product image
        if($imageData['isProductImage']){
          $manager = new ImageManager();
          $newImage = $manager->make($path.$name);

          $prefix = 'm-';
          for($ratio = 1.5; $ratio <= 2; $ratio+=0.5) {
            $width = $newImage->width() / $ratio;
            $height = $newImage->height() / $ratio;
            $newImage->resize($width, $height);
            $newImage->save($path.$prefix.$name);

            // change prefix
            $prefix = 'thumb-';
          }
        }

        return Response::json([
            'error' => false,
            'code'  => 200,
            'id'    => $image->id,
            'image' => $name,
        ], 200);

    }

    /**
     * Delete Image From Session folder, based on original filename
     */
    public function delete($imageID)
    {
        $image = Image::find($imageID);

        if(!empty($image)) {
            $name = $image->name;
            $path = $image->path;
            $filePath = [
              $path.$name,
              $path.'m-'.$name,
              $path.'thumb-'.$name
            ];

            foreach($filePath as $path) {
                if(File::exists(public_path().$path)) {
                    File::delete(public_path().$path);
                }
            }

            $image->delete();

            return Response::json([
                'error' => false,
                'code' => 200,
            ], 200);

        }
        else {
            return Response::json([
                'error' => true,
                'code'  => 400
            ], 400);
        }
    }
}
