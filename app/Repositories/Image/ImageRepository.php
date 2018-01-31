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

        if(array_key_exists('personalization', $imageData)) {
            if(substr($name, -3) == 'png') {
                $im = ImageCreateFromPng($path.$name);
                header('Content-Type: image/jpeg');
                ImageJpeg($im, $path.$name);
                Imagedestroy($im);
            }

            $blackImg = ImageCreateFromJpeg($path.$name);
            $whiteImg = ImageCreateFromJpeg($path.$name);
            $width = imagesx($blackImg);
            $height = imagesy($blackImg);
            imagefilter($blackImg, IMG_FILTER_GRAYSCALE);

            for ($i=0; $i<$width; $i++)
            {
                for ($j=0; $j<$height; $j++)
                {

                    // Get the RGB value for current pixel
                    $rgb = ImageColorAt($blackImg, $i, $j);

                    // Extract each value for: R, G, B
                    $rr = ($rgb >> 16) & 0xFF;
                    $gg = ($rgb >> 8) & 0xFF;
                    $bb = $rgb & 0xFF;

                    $g1 = ($rr > 225 && $gg > 225 && $bb > 225) ? 0xFF : 0x00;
                    $g2 = ($rr > 225 && $gg > 225 && $bb > 225) ? 0x00 : 0xFF;
                    $black = imagecolorallocate($blackImg, $g1, $g1, $g1);
                    $white = imageColorallocate($whiteImg, $g2, $g2, $g2);

                    imagesetpixel($blackImg, $i, $j, $black);
                    imagesetpixel($whiteImg, $i, $j, $white);
                }
            }

            $white = imagecolorallocate($blackImg, 255, 255, 255);
            imagecolortransparent($blackImg, $white);
            ImagePng($blackImg, $path.'black-'.$name);
            Imagedestroy($blackImg);
            $black = imagecolorallocate($whiteImg, 0, 0, 0);
            imagecolortransparent($whiteImg, $black);
            ImagePng($whiteImg, $path.'white-'.$name);
            Imagedestroy($whiteImg);
        }


        // insert into database
        $image = new Image([
          'name' => $name,
          'path' => $path,
          'isProductImage' => request('isProductImage', 0)
        ]);

        $image->save();

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
