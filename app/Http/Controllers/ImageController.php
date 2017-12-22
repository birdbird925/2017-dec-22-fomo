<?php

namespace App\Http\Controllers;

use App\Repositories\Image\ImageRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\Image;

class ImageController extends Controller
{
    protected $image;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->repository = $imageRepository;
    }

    public function uploadImage()
    {
        $file = request()->file('file');
        $isProductImage = request('isProductImage', 0);
        return $this->repository->upload(['file' => $file, 'isProductImage' => $isProductImage]);
    }

    public function deleteImage()
    {
        $imageID = request('id');
        if(!$imageID)
            return 0;

        $response = $this->repository->delete($imageID);
    }

    public function imageSrc($id)
    {
        $image = Image::find($id);
        return $image ? $image->getSrc() : 0;
    }
}
