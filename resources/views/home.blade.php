@extends('layouts.app')

@section('body-class')
    initial
@endsection

@section('content')
    @if(sizeof($slider) != 0)
    <ul class="hero-slider" id="lightSlider">
        @foreach($slider as $slide)
            <li style="background-image: url({{$slide->image->getSrc()}})"></li>
        @endforeach
    </ul>
    @else
    <div class="hero-image"></div>
    @endif

    <div class="featured-products" id="featured">
        @if($featuredProduct->count() != 0)
            @foreach($featuredProduct as $feature)
                <div class="product">
                    <div class="bg" style="background-image: url({{$feature->image->getSrc()}})"></div>
                    <div class="box col-sm-4 col-xs-12" product-link="/customize/{{$feature->product->id}}">
                        <div class="description-wrapper">
                            <div class="description-position">
                                <div class="name">{{$feature->product->name}}</div>
                                <div class="description">
                                    {!!$feature->product->description!!}
                                    <br>
                                    <span class="price">
                                        @if(Session::get('currency') != '')
                                            {{Session::get('currency')}} {{$feature->product->priceBasedCurrency(Session::get('currency'))}}
                                        @else
                                            MYR {{$feature->product->priceBasedCurrency('MYR')}}
                                        @endif
                                    </span>
                                </div>
                                <a href="/customize/{{$feature->product->id}}" class="customize-btn">Customize</a>
                            </div>
                        </div>
                        <div class="image-wrapper">
                            <img src="{{$feature->product->image}}">
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="product">
                <div class="bg" style="background-image: url(/images/demo/3.jpg)"></div>
                <div class="box col-sm-4 col-xs-12" product-link="/customize">
                    <div class="description-wrapper">
                        <div class="description-position">
                            <div class="name">RULAJ</div>
                            <div class="description">
                                NH35A, Japan made movement /Automatic winding with ball bearing /Bi-directional winding / Hacking seconds hand / Raven black genuine leather /
                                <br>
                                <span class="price">$300</span>
                            </div>
                            <a href="/customize">Customize</a>
                        </div>
                    </div>
                    <div class="image-wrapper">
                        <img src="/images/demo/Watch02.png">
                    </div>
                </div>
            </div>
            <div class="product">
                <div class="bg" style="background-image: url(/images/demo/2.jpg)"></div>
                <div class="box col-sm-4 col-xs-12" product-link="/customize">
                    <div class="description-wrapper">
                        <div class="description-position">
                            <div class="name">RULAJ</div>
                            <div class="description">
                                NH35A, Japan made movement /Automatic winding with ball bearing /Bi-directional winding / Hacking seconds hand / Raven black genuine leather /
                                <br>
                                <span class="price">$300</span>
                            </div>
                            <a href="/customize">Customize</a>
                        </div>
                    </div>
                    <div class="image-wrapper">
                        <img src="/images/demo/Watch03.png">
                    </div>
                </div>
            </div>
            <div class="product">
                <div class="bg" style="background-image: url(/images/demo/1.jpg)"></div>
                <div class="box col-sm-4 col-xs-12" product-link="/customize">
                    <div class="description-wrapper">
                        <div class="description-position">
                            <div class="name">RULAJ</div>
                            <div class="description">
                                NH35A, Japan made movement /Automatic winding with ball bearing /Bi-directional winding / Hacking seconds hand / Raven black genuine leather /
                                <br>
                                <span class="price">$300</span>
                            </div>
                            <a href="/customize">Customize</a>
                        </div>
                    </div>
                    <div class="image-wrapper">
                        <img src="/images/demo/Watch04.png">
                    </div>
                </div>
            </div>
            <div class="product">
                <div class="bg" style="background-image: url(/images/demo/3.jpg)"></div>
                <div class="box col-sm-4 col-xs-12"  product-link="/customize">
                    <div class="image-wrapper">
                        <img src="/images/demo/Watch01.png">
                    </div>
                    <div class="description-wrapper">
                        <div class="description-position">
                            <div class="name">RULAJ</div>
                            <div class="description">
                                NH35A, Japan made movement /Automatic winding with ball bearing /Bi-directional winding / Hacking seconds hand / Raven black genuine leather /
                                <br>
                                <span class="price">$300</span>
                            </div>
                            <a href="/customize">Customize</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="title">#FOMOWATCH</div>
    <div id="instafeed"></div>
@endsection
