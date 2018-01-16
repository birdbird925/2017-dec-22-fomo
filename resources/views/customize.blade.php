@extends('layouts.app')

@section('logo-class')
    fixed
@endsection

@section('footer-class')
    hide
@endsection

@section('content')
    <div class="customize-wrapper">
        <div class="customize-canvas">
            <div class="canvas-slider">
                <li><div id="front-canvas" class="initial"></div></li>
                <li><div id="back-canvas" class="initial"></div></li>
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
        <div class="customize-option">
            <input type="hidden" name="customize-name" value="{{$name}}">
            <input type="hidden" name="customize-product" value="{{$component}}">
            <div class="desktop-control prev hide">Previous Step</div>
            <div class="desktop-control next">Next Step</div>
            <ul class="option-slider" id="lightSlider">
                @foreach($steps as $sIndex=>$step)
                    <li class="step {{$step->type_id ? 'customize-element customize'.$step->type_id : '' }}" main-title="{{$step->title}}" extral-title="{{$step->extral_title}}" {{$step->completeInfo()}}>
                        <div class="option-header">
                            @if($step->previousStep())
                                <div class="pull-left control prev {{$step->previousStep()->type_id ? 'customize-element customize'.$step->previousStep()->type_id : ''}}">
                                    {{$step->previousStep()->title}}
                                </div>
                            @endif
                            @if($step->nextStep())
                                <div class="pull-right control next {{$step->nextStep()->type_id ? 'customize-element customize'.$step->nextStep()->type_id : ''}}">
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
                                            <input id="type{{$type->id}}" type="radio" name="customize_type" {{$type->radioAttr()}} description="{{$type->description}}" size-image="{{$type->size_image}}">
                                        </div>
                                    @endforeach
                                @else
                                    @if($step->componentByLevel(1)->count() > 1)
                                        @foreach($step->componentByLevel(1) as $component)
                                            <div class="form-group {{$component->type_id ? 'customize-element customize'.$component->type_id : 'fixed-element fadeIn'}} {{$component->available ? '' : 'disabled'}}">
                                                <label class="step{{$step->id}} {{$component->type}}-option" for="component{{$component->id}}">
                                                    @if($component->type == 'image')
                                                        <span style="mask-image: url({{$component->image('value')->getSrc()}}); -webkit-mask-image: url({{$component->image('value')->getSrc()}})"></span>
                                                    @elseif($component->type == 'color')
                                                        <span style="background: {{$component->value}}"></span>
                                                    @else
                                                        {{$component->value}}
                                                    @endif
                                                </label>
                                                <input id="component{{$component->id}}" type="radio" name="step{{$step->id}}" {{$component->radioAttr($step->id)}} description="{{$component->description}}" size-image="{{$component->size_image}}" {{$component->available ? '' : 'disabled'}}>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if($step->componentByLevel(2)->count() > 0)
                                    </div>{{-- .main-option --}}
                                    <div class="title customize-element customize1">{{$step->componentByLevel(2)->first()->level_title}}</div>
                                    <div class="main-option">
                                        @foreach($step->componentByLevel(2) as $component)
                                            <div class="form-group {{$component->type_id ? 'customize-element customize'.$component->type_id : 'fixed-element fadeIn'}} {{$component->available ? '' : 'disabled'}}">
                                                <label class="step{{$step->id}}-2 {{$component->type}}-option" for="component{{$component->id}}">
                                                    @if($component->type == 'image')
                                                        <span style="mask-image: url({{$component->image('value')->getSrc()}}); -webkit-mask-image: url({{$component->image('value')->getSrc()}})"></span>
                                                    @elseif($component->type == 'color')
                                                        <span style="background: {{$component->value}}"></span>
                                                    @else
                                                        {{$component->value}}
                                                    @endif
                                                </label>
                                                <input id="component{{$component->id}}" type="radio" name="step{{$step->id}}-2" {{$component->radioAttr($step->id)}} description="{{$component->description}}" size-image="{{$component->size_image}}" {{$component->available ? '' : 'disabled'}}>
                                            </div>
                                        @endforeach
                                    @endif
                                @endif
                            </div>
                            @if($step->extralOption()->count() > 0)
                                <div class="extral-option">
                                    @foreach($step->extralOption() as $extralOption)
                                        <div class="form-group extral{{$step->id}} component-element component{{$extralOption->component_id}} {{$extralOption->available ? '' : 'disabled'}}">
                                            <label class="step{{$step->id}}_extral {{$extralOption->type}}-option" for="extral{{$extralOption->id}}">
                                                @if($extralOption->type == 'image')
                                                    <span style="mask-image: url({{$extralOption->image('value')->getSrc()}}); -webkit-mask-image: url({{$extralOption->image('value')->getSrc()}})"></span>
                                                @elseif($extralOption->type == 'color')
                                                    <span style="background: {{$extralOption->value}}"></span>
                                                @else
                                                    {{$extralOption->value}}
                                                @endif
                                            </label>
                                            <input id="extral{{$extralOption->id}}" type="radio" name="step{{$step->id}}_extral" {{$extralOption->radioAttr()}} description="{{$extralOption->description}}" size-image="{{$extralOption->size_image}}" {{$extralOption->available ? '' : 'disabled'}}>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if($step->personalizeOption()->count() > 0)
                                @foreach($step->personalizeOption() as $personalize)
                                    @if($personalize->personalize == 'text')
                                        <div class="form-group step{{$step->id}}personalize step{{$step->id}}personalize{{$personalize->id}} personalize-text {{$personalize->type_id ? 'customize-element customize'.$personalize->type_id : 'fixed-element'}}">
                                            <div class="title">
                                                Key in the text (up to  12 letter)
                                            </div>
                                            <input type="text" name="step{{$step->id}}personalize{{$personalize->id}}" placeholder="------------" layer="{{$personalize->personalize ? $personalize->layer : 0}}">
                                        </div>
                                    @endif

                                    @if($personalize->personalize == 'image')
                                        <div class="form-group step{{$step->id}}personalize step{{$step->id}}personalize{{$personalize->id}} personalize-image {{$personalize->type_id ? 'customize-element customize'.$personalize->type_id : 'fixed-element'}}">
                                            <div class="title">
                                                PNG OR SVG FILES ARE RECOMMENDED
                                            </div>
                                            <label for="personalize-image" class="file-label"></label>
                                            <input id="personalize-image" name="step{{$step->id}}personalize{{$personalize->id}}" type="file" layer="{{$personalize->personalize ? $personalize->layer : 0}}">
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            <div class="description">
                                <div class="main"></div>
                                <div class="extral"></div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
