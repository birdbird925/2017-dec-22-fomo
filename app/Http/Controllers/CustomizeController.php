<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Swap\Laravel\Facades\Swap;
use App\SavedProduct;
use App\CustomizeProduct;
use App\CustomizeType;
use App\CustomizeStep;
use App\CustomizeComponent;
use App\CustomizeComponentOption;

class CustomizeController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'auth',
            'auth.admin'
        ], [
            'except' => [
                'test',
                'index',
                'edit',
                'addCart',
                'productType',
                'productDescription',
                'saveProduct'
            ]
        ]);
    }

    public function index()
    {
        $types = CustomizeType::all();
        $steps = CustomizeStep::orderBy('id')->get();
        $nameList = ['Abraxane', 'Baycox', 'Dafiro', 'Lanzo', 'Qutenza', 'Xentic', 'Ranacox'];
        $name = $nameList[array_rand($nameList)];
        $component = '';
        $cartItem = '';
        $product = '';

        return view('customize', compact('types', 'steps', 'component', 'name', 'cartItem', 'product'));
    }

    public function edit($id)
    {
        $component = '';
        $name = '';
        $cartItem = '';
        $product = '';
        // cart product
        if(substr($id, 0, 2) == 'SS') {
            foreach(session('cart.item') as $item) {
                if($item['code'] == $id) {
                    $component = $item['product'];
                    $name = $item['name'];
                    $cartItem = $id;
                }
            }
        }
        else {
            $customizeProduct = customizeProduct::find($id);
            if(!$customizeProduct){
                return redirect('/customize');
            }
            else {
                $component = $customizeProduct->components;
                $name = $customizeProduct->name;
                $product = $customizeProduct;

                if(!$product->checkComponentStatus())
                    session()->flash('popup', [
                        'title' => 'Oops!',
                        'caption' => 'Found some component of this watch are missing.'
                    ]);
            }
        }

        $types = CustomizeType::all();
        $steps = CustomizeStep::orderBy('id')->get();
        return view('customize', compact('types', 'steps', 'component', 'name', 'cartItem', 'product'));
    }

    public function addCart()
    {
        $code  = 'SS'.substr(md5(microtime()),rand(0,26),12);
        $usdPrice = $this->productType(request('product'))->usd_price;
        $sgdPrice = $this->productType(request('product'))->sgd_price;
        $myrPrice = $this->productType(request('product'))->myr_price;
        $euPrice = $this->productType(request('product'))->eu_price;

        session()->push('cart.item', [
            'code' => $code,
            'name' => request('name'),
            'product' => request('product'),
            'images' => request('images'),
            'thumb' => request('thumb'),
            'back' => request('back'),
            'USD_price' => $usdPrice,
            'SGD_price' => $sgdPrice,
            'MYR_price' => $myrPrice,
            'EUR_price' => $euPrice,
            'description' => $this->productDescription(request('product')),
            'quantity' => 1,
        ]);

        $total = 0;
        $priceField = session('currency').'_price';
        foreach(session('cart.item') as $item)
            $total += ($item[$priceField] * $item['quantity']);
        session(['cart.total' => $total]);

        return $code;
    }

    public function saveProduct()
    {
        if(Auth::check()) {
            $priceKey = '';
            switch(session('currency')) {
                case 'USD':
                    $priceKey = 'usd_price';
                    break;

                case 'SGD':
                    $priceKey = 'sgd_price';
                    break;

                case 'MYR':
                    $priceKey = 'myr_price';
                    break;

                case 'EURO':
                    $priceKey = 'eu_price';
                    break;
            }

            $product = CustomizeProduct::create([
                'name' => request('name'),
                'components' => request('product'),
                'images' => request('images'),
                'thumb' => request('thumb'),
                'back' => request('back'),
                'type_id' => $this->productType(request('product'))->id,
                'description' => $this->productDescription(request('product')),
                'price' => $this->productType(request('product'))->$priceKey,
                'created_by' => Auth::user()->id,
            ]);

            $savedProduct = SavedProduct::create([
                'user_id' => Auth::user()->id,
                'product_id' => $product->id
            ]);

            return $product->id;
        }
    }

    public function updateProduct($id)
    {
        if(Auth::check()) {
            $product = CustomizeProduct::find($id);
            if(Auth::user()->checkSavedProduct($id) > 0) {
                $product->update([
                    'name' => request('name'),
                    'components' => request('product'),
                    'images' => request('images'),
                    'thumb' => request('thumb'),
                    'back' => request('back'),
                    'type_id' => $this->productType(request('product'))->id,
                    'description' => $this->productDescription(request('product')),
                ]);
                $product->save();
            }
        }
    }

    protected function productType($product)
    {
        foreach(json_decode($product) as $inputName=>$attritube) {
            if($inputName == 'customize_type') {
                $customize = CustomizeType::find($attritube->value);
                return $customize;
            }
        }
    }

    protected function productDescription($product)
    {
        $description = '<ul>';
        $type = 'Meca-quartz';

        Ronda Caliber 762 Quartz Movement
        foreach(json_decode($product) as $inputName=>$attritube) {
            // size
            if($inputName == 'step2') {
                $type = $attritube->value == '128' ? 'Meca-quartz' : 'Quartz' ;
                $description .= '<li>316L Stainless Steel Case in '.($attritube->value == '131' ? '36' : '40').'mm excl. Crown'
            }
        }
        $description .= '<li>Sapphire Cystal</li>';
        $description .= '<li>5ATM/50 Meters Water Resistant</li>';
        $description .= '<li>'.($type == 'Meca-quartz' ? 'SEIKO VK61 Hybrid Meca-Quartz Chronograph' : 'Ronda Caliber 762 Quartz Movement' ).'</li>';
        $description .= '<li>Custom-Made Dial with Back Case Engraving</li>';
        $description .= '</ul>';
        // $personalize = false;
        // $engrave = false;
        // $error = false;
        // $description = '';
        // foreach(json_decode($product) as $inputName=>$attritube) {
        //     if($inputName == 'customize_type') {
        //         $customize = CustomizeType::find($attritube->value);
        //         $description .= $customize->name.' / ';
        //     }
        //
        //     // if input was main radio button
        //     else if(preg_replace('/[0-9]+/', '', $inputName) == 'step') {
        //         $component = CustomizeComponent::find($attritube->value);
        //         if($component->checkType($customize->id)) {
        //             $step = $component->step->title;
        //             if($component->size_component && $component->personalize == '')
        //                 $description .= $component->value.' / ';
        //             else if($component->option->count() == 0 && $component->personalize == '')
        //                 $description .= $component->value.' '.$step.' / ';
        //         }
        //         else $error = true;
        //     }
        //     // if input was extral radio button
        //     else if(substr($inputName, -6) == 'extral') {
        //         $extral = CustomizeComponentOption::find($attritube->value);
        //         $main = $extral->component;
        //         $step = $main->step;
        //
        //         $description .= $main->type == 'image' ? '#'.$main->value.' '.$step->title : $main->value;
        //         $description .= ' in '.$extral->value.' / ';
        //     }
        //     // input is personalize item
        //     else if (strpos($inputName, 'personalize') !== false){
        //         $stepID = substr($inputName, 0, strpos($inputName, 'personalize'));
        //         $stepID = (int)str_replace("step", "", $stepID);
        //         $step = CustomizeStep::find($stepID);
        //
        //         if(isset($attritube->rotation)) {
        //             if(strtolower($step->title) == 'personalize') $personalize = true;
        //             if(strtolower($step->title) == 'engrave') $engrave = true;
        //             $description .= 'With '.$step->title.' image'.' / ';
        //         }
        //         else if($attritube->value != '') {
        //             if(strtolower($step->title) == 'personalize') $personalize = true;
        //             if(strtolower($step->title) == 'engrave') $engrave = true;
        //
        //             $description .= 'With '.$step->title.' text'.' / ';
        //         }
        //     }
        //     else {
        //         $component = CustomizeComponent::find($attritube->value);
        //         $description .= $component->value .' '.$component->level_title.' / ';
        //     }
        // }
        //
        // if(!$personalize) $description .= 'Without Personalize / ';
        // if(!$engrave) $description .= 'Without Engrave / ';

        return $description;
    }

    protected function uploadProductImage($image) {
        // upload product image
        define('UPLOAD_DIR', 'images/');
	    $img = $image;
	    $img = str_replace('data:image/png;base64,', '', $img);
	    $img = str_replace(' ', '+', $img);
	    $data = base64_decode($img);
	    $file = UPLOAD_DIR . uniqid() . '.png';
	    $success = file_put_contents($file, $data);

        return '/'.$file;
    }

    // admin
    public function adminSteps()
    {
        $steps = CustomizeStep::all();
        return view('admin.customize.step.index', compact('steps'));
    }

    public function adminStep($id)
    {
        $step = CustomizeStep::find($id);
        if(!$step) abort('404');
        $types = CustomizeType::all();
        return view('admin.customize.step.show', compact('step', 'types'));
    }

    public function adminUpdateStep($id, Request $request)
    {
        $step = CustomizeStep::find($id);
        if(!$step) abort('404');

        $this->validate($request, [
            'main_title' => 'required'
        ]);
        $step->title = $request->main_title;
        $step->extral_title = $request->sub_title;
        $step->save();

        return redirect('/admin/customize/step');
    }

    public function adminProducts()
    {
        $products = CustomizeProduct::where('created_by', Auth::user()->id)->get();
        return view('admin.customize.product.index', compact('products'));
    }

    public function adminProduct($id)
    {
        $product = CustomizeProduct::find($id);
        if(!$product) abort('404');
        return view('admin.customize.product.show', compact('product'));
    }

    public function adminProductStore()
    {
        $priceKey = '';
        switch(session('currency')) {
            case 'USD':
                $priceKey = 'usd_price';
                break;

            case 'SGD':
                $priceKey = 'sgd_price';
                break;

            case 'MYR':
                $priceKey = 'myr_price';
                break;

            case 'EURO':
                $priceKey = 'eu_price';
                break;
        }
        $product = CustomizeProduct::create([
            'name' => request('name'),
            'components' => request('product'),
            'image' => $this->uploadProductImage(request('image')),
            'images' => request('images'),
            'thumb' => request('thumb'),
            'back' => request('back'),
            'type_id' => $this->productType(request('product'))->id,
            'description' => $this->productDescription(request('product')),
            'price' => $this->productType(request('product'))->$priceKey,
            'created_by' => Auth::user()->id,
        ]);
    }

    public function adminProductUpdate($id)
    {
        $product = CustomizeProduct::find($id);
        if($product)
            $product->update([
                'name' => request('name'),
                'components' => request('product'),
                'image' => $this->uploadProductImage(request('image')),
                'images' => request('images'),
                'thumb' => request('thumb'),
                'back' => request('back'),
                'type_id' => $this->productType(request('product'))->id,
                'description' => $this->productDescription(request('product')),
                'price' => $this->productType(request('product'))->price,
                'created_by' => Auth::user()->id,
            ]);
    }

    public function adminProductDelete($id)
    {
        $product = CustomizeProduct::find($id);
        $product->delete();
        return redirect('/admin/customize/product');
    }

    public function adminTypes()
    {
        $types = CustomizeType::all();
        return view('admin.customize.type.index', compact('types'));
    }

    public function adminType($id)
    {
        $type = CustomizeType::find($id);
        if(!$type) abort('404');
        $steps = CustomizeStep::all();
        return view('admin.customize.type.show', compact('type', 'steps'));
    }

    public function adminTypeUpdate($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'price' => 'required|numeric|min:1',
            'description' => 'required'
        ]);
        $type = CustomizeType::find($id);
        if(!$type) abort('404');
        $type->update([
            'name' => request('name'),
            'price' => request('price'),
            'description' => request('description'),
        ]);
        return redirect('/admin/customize/type');
    }

    public function adminComponent($id)
    {
        $component = CustomizeComponent::find($id);
        if(!$component) abort('404');
        return view('admin.customize.component.show', compact('component'));
    }
}
