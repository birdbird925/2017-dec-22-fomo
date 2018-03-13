@extends('layouts.app')

@section('logo-class')
    fixed
@endsection

@section('footer-class')
    hide
@endsection

@section('facebook.pixel.event')
    fbq('track', 'ViewContent');
@endsection

@section('content')
    <div class="customize-wrapper">
        <div class="customize-canvas">
            @include('layouts.partials.preloader')
            <div class="canvas-slider-wrapper">
                <div class="canvas-slider">
                    <li><div id="front-canvas" class="initial"></div></li>
                    <li><div id="back-canvas" class="initial"></div></li>
                </div>
            </div>
            <ul class="customize-controls">
                @if(Auth::check() && Auth::user()->checkRole('admin'))
                    <li class="customize-control admin-control" data-action="{{$product == '' ? 'save' : 'update'}}" data-id="{{$product != '' ? $product->id : ''}}"></li>
                @else
                    <li class="customize-control {{ $cartItem != '' ? 'addedCart' : 'addCart'}}" data-id="{{ $cartItem != '' ? $cartItem : '' }}"></li>
                    <li class="customize-control {{ Auth::check() && $product != '' ? (Auth::user()->checkSavedProduct($product->id) > 0 ? 'saved' : 'save')  : 'save' }}" data-id="{{ Auth::check() && $product != '' ? ($product->created_by == Auth::user()->id ? $product->id : '')  : '' }}"></li>
                @endif
            </ul>
        </div>
        <div class="customize-option lock">
            <div class="protection-layer"></div>
            <input type="hidden" name="customize-name" value="{{$name}}">
            <input type="hidden" name="customize-product" value="{{$component}}">
            <div class="desktop-control prev hide">Previous Step</div>
            <div class="desktop-control next">Next Step</div>
            <ul class="option-slider" id="lightSlider">
                @foreach($steps as $sIndex=>$step)
                    <li class="step step{{$step->id}} {{$step->type_id ? 'step-for-'.$step->type_id : '' }}" data-title="{{$step->title}}" {{$step->completeInfo()}}>
                        <div class="option-header">
                            @if($step->previousStep())
                                <div class="pull-left control prev" data-default="{{$step->previousStep()->title}}">
                                    {{$step->previousStep()->title}}
                                </div>
                            @endif
                            @if($step->nextStep())
                                <div class="pull-right control next" data-default="{{$step->nextStep()->title}}">
                                    {{$step->nextStep()->title}}
                                </div>
                            @endif
                            <span class="header-title">{{$step->title}}</span>
                        </div>
                        <div class="option-wrapper">
                            <div class="main-option">
                                @if($step->primary)
                                    @foreach($types as $type)
                                        <div class="form-group">
                                            <label class="customize_type" for="type{{$type->id}}">{{$type->name}}</label>
                                            <input id="type{{$type->id}}" type="radio" name="customize_type" {{$type->radioAttr()}} description="{{$type->description}}" size-image="{{$type->size_image}}" hide-step=".step-for-{{$type->id == 1 ? '2' : '1'}}">
                                        </div>
                                    @endforeach
                                @else
                                    @if($step->component->count() > 0)
                                        @foreach($step->component as $component)
                                            <div class="form-group {{$component->required_component ? 'colorOption'.$component->required_component : ''}} {{$component->type_id ? 'customize-element customize'.$component->type_id : 'fixed-element fadeIn'}} {{$component->available ? '' : 'disabled'}}">
                                                <label class="step{{$step->id}} {{$component->type}}-option" for="component{{$component->id}}">
                                                    @if($component->type == 'image')
                                                        @if(substr($component->image('value')->getSrc(), -3) == 'png')
                                                            <span class="png" style="background-image: url({{$component->image('value')->getSrc()}});"></span>
                                                        @else
                                                            <span class="svg" style="mask-image: url({{$component->image('value')->getSrc()}}); -webkit-mask-image: url({{$component->image('value')->getSrc()}})"></span>
                                                        @endif
                                                    @elseif($component->type == 'color')
                                                        <span style="background: {{$component->value}}"></span>
                                                    @else
                                                        {{$component->value}}
                                                    @endif
                                                </label>
                                                <input id="component{{$component->id}}" type="radio" name="step{{$step->id}}" {{$component->radioAttr($step->id)}} description="{{$component->description}}" size-image="{{$component->size_image}}" required-component="{{$component->required_component}}" {{$component->available ? '' : 'disabled'}}>
                                            </div>
                                        @endforeach
                                    @endif
                                @endif
                            </div>
                            @if($step->personalizeOption()->count() > 0)
                                @foreach($step->personalizeOption() as $personalize)
                                    @if($personalize->personalize == 'text')
                                        <div class="form-group personalization step{{$step->id}}personalize step{{$step->id}}personalize{{$personalize->id}} personalize-text {{$personalize->type_id ? 'customize-element customize'.$personalize->type_id : 'fixed-element'}}">
                                            @if($step->id == 13)
                                                <div class="title">
                                                    Key in the text (up to  10 letter)
                                                </div>
                                                <input type="text" name="step{{$step->id}}personalize{{$personalize->id}}" placeholder="----------" layer="{{$personalize->personalize ? $personalize->layer : 0}}" maxlength="10">
                                            @else
                                                <div class="title">
                                                    Key in the text for line 1 (up to 18 letter)
                                                </div>
                                                <input type="text" name="step{{$step->id}}personalize{{$personalize->id}}line1" line='1' placeholder="------------------" layer="{{$personalize->personalize ? $personalize->layer : 0}}" maxlength="18">
                                                <br>
                                                <div class="title">
                                                    Key in the text for line 2 (up to 18 letter)
                                                </div>
                                                <input type="text" name="step{{$step->id}}personalize{{$personalize->id}}line2" line='2' placeholder="------------------" layer="{{$personalize->personalize ? $personalize->layer : 0}}" maxlength="18">
                                            @endif
                                        </div>
                                    @endif

                                    @if($personalize->personalize == 'image')
                                        <div class="form-group personalization step{{$step->id}}personalize step{{$step->id}}personalize{{$personalize->id}} personalize-image {{$personalize->type_id ? 'customize-element customize'.$personalize->type_id : 'fixed-element'}}">
                                            <div class="title">
                                                JPG & PNG FILES ARE RECOMMENDED
                                            </div>
                                            <label for="personalize-image{{$step->id}}" class="file-label"></label>
                                            <input id="personalize-image{{$step->id}}" name="step{{$step->id}}personalize{{$personalize->id}}" type="file" layer="{{$personalize->personalize ? $personalize->layer : 0}}" accept="image/x-png,image/jpeg" >
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            <div class="description">
                                <div class="main"></div>
                                <div class="extral"></div>
                            </div>
                            {{-- add to cart --}}
                            @if($sIndex == $steps->count()-1)
                                <a href="#" class="addCart">ADD TO CART</a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
