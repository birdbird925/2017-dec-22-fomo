<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\Image\ImageRepository;
use App\Slider;
use App\FeaturedProduct;
use App\CustomizeProduct;

class CmsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','auth.admin']);
    }
    
    public function index()
    {
        $slider = Slider::all();
        $featured = FeaturedProduct::all();
        $nav = DB::select("select * from cms_menu where type = 'nav'");
        $footer = DB::select("select * from cms_menu where type = 'footer'");
        $pages = DB::select('select * from cms_page');
        return view('admin.cms.index', compact('slider', 'featured', 'nav', 'footer', 'pages'));
    }

    public function create($type)
    {
        if($type == 'featured')
            $products = CustomizeProduct::where('created_by', Auth::user()->id)->get();

        return view('admin.cms.'.$type, compact('products'));
    }

    public function store($type, Request $request)
    {
        $imageRepository = new ImageRepository();
        switch($type) {
            case 'featured':
                $this->validate($request, [
                    'product_id' => 'required',
                    'background' => 'required|image',
                ]);

                $imageID = $imageRepository->upload(['file' => request()->file('background'), 'isProductImage' => 0])->getData()->id;
                $featured = FeaturedProduct::create([
                    'product_id' => request('product_id'),
                    'background' => $imageID
                ]);
                break;

            case 'slider':
                $this->validate($request, ['image' => 'required|image']);
                $imageID = $imageRepository->upload(['file' => request()->file('image'), 'isProductImage' => 0])->getData()->id;
                $slider = Slider::create(['image_id' => $imageID]);
                break;

            case 'menu':
                $this->validate($request, [
                    'type' => 'required',
                    'text' => 'required'
                ]);

                if (filter_var($request->link, FILTER_VALIDATE_URL) === FALSE)
                    $link = '/'.$request->link;
                else
                    $link = $request->link;

                DB::table('cms_menu')->insert([
                    'type' => $request->type,
                    'text' => $request->text,
                    'link' => $link
                ]);
                break;

            case 'page':
                $this->validate($request, [
                    'page_title' => 'required',
                    'page_content' => 'required',
                    'page_slug' => 'required|unique:cms_page|regex:/^\S*$/u'
                ]);

                DB::table('cms_page')->insert([
                    'page_slug' => $request->page_slug,
                    'page_title' => $request->page_title,
                    'page_content' => $request->page_content,
                    'page_content_size' => $request->page_content_size,
                    'seo_title' => $request->seo_title,
                    'seo_description' => $request->seo_description,
                    'seo_keyword' => $request->sea_keyword,
                ]);
                break;

        }

        return redirect('/admin/cms');
    }

    public function edit($type, $id)
    {
        switch($type) {
            case 'featured':
                $products = CustomizeProduct::where('created_by', Auth::user()->id)->get();
                $content = FeaturedProduct::find($id);
                break;

            case 'slider':
                $content = Slider::find($id);
                break;

            case 'menu':
                $content = DB::select("select * from cms_menu where id = '".$id."'");
                break;

            case 'page':
                $content = DB::select("select * from cms_page where id = '".$id."'");
                break;

            default:
                abort(404);
                break;
        }

        if(!$content)
            abort(404);

        return view('admin.cms.'.$type, compact('content', 'products'));
    }

    public function update($type, $id, Request $request)
    {
        $imageRepository = new ImageRepository();
        switch($type) {
            case 'featured':
                $featured = FeaturedProduct::find($id);
                if(!$featured) return redirect('/admin/cms');

                $this->validate($request, [
                    'product_id' => 'required',
                    'background' => 'image',
                ]);

                $featured->product_id = $request->product_id;
                if($request->background != null) {
                    $deleteImage = $featured->background;
                    $imageID = $imageRepository->upload(['file' => request()->file('background'), 'isProductImage' => 0])->getData()->id;
                    $featured->background = $imageID;
                    $featured->save();
                    $imageRepository->delete($deleteImage);
                }

                $featured->save();
                break;

            case 'slider':
                $slider = Slider::find($id);
                if(!$slider) return redirect('/admin/cms');

                $this->validate($request, ['image' => 'required|image']);
                $imageID = $imageRepository->upload(['file' => request()->file('image'), 'isProductImage' => 0])->getData()->id;
                $deleteImage = $slider->image_id;
                $slider->image_id = $imageID;
                $slider->save();
                $imageRepository->delete($deleteImage);
                break;

            case 'menu':
                $this->validate($request, [
                    'type' => 'required',
                    'text' => 'required'
                ]);

                if (filter_var($request->link, FILTER_VALIDATE_URL) === FALSE)
                    $link = '/'.$request->link;
                else
                    $link = $request->link;

                DB::table('cms_menu')
                    ->where('id', $id)
                    ->update([
                        'type' => $request->type,
                        'text' => $request->text,
                        'link' => $link
                    ]);
                break;

            case 'page':
                $this->validate($request, [
                    'page_title' => 'required',
                    'page_content' => 'required',
                    'page_slug' => 'required|unique:cms_page,page_slug,'.$id.'|regex:/^\S*$/u'
                ]);

                DB::table('cms_page')
                    ->where('id', $id)
                    ->update([
                    'page_slug' => $request->page_slug,
                    'page_title' => $request->page_title,
                    'page_content' => $request->page_content,
                    'page_content_size' => $request->page_content_size,
                    'seo_title' => $request->seo_title,
                    'seo_description' => $request->seo_description,
                    'seo_keyword' => $request->sea_keyword,
                ]);
                break;
        }

        return redirect('/admin/cms');
    }

    public function delete($type, $id)
    {
        $imageRepository = new ImageRepository();
        switch($type) {
            case 'featured':
                $featured = FeaturedProduct::find($id);
                $imageRepository->delete($featured->background);
                if(!$featured) return redirect('/admin/cms');
                $featured->delete();
                break;

            case 'slider':
                $slider = Slider::find($id);
                $imageRepository->delete($slider->image_id);
                if(!$slider) return redirect('/admin/cms');
                $slider->delete();
                break;

            case 'menu':
                DB::table('cms_menu')->where('id', $id)->delete();
                break;

            case 'page':
                DB::table('cms_page')->where('id', $id)->delete();
                break;
        }

        return redirect('/admin/cms');
    }

    public function page($slug)
    {
        $page = DB::select('select * from cms_page where page_slug = ? LIMIT 1', [$slug]);
        if(sizeof($page) == 0) {
            abort(404);
        }
        else {
            $page = $page[0];
            return view('page', compact('page'));
        }
    }

}
