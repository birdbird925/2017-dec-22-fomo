@extends('layouts.admin')

@section('page-direction')
    CMS
@endsection

@section('cms-sidebar')
    active
@endsection

@section('content')
    <div class="col-sm-8">
        <div class="card">
            <div class="header">
                <h4 class="title">Featured Product</h4>
            </div>
            <div class="content">
                @if($featured->count() == 0)
                    <p>You are not defined any featured Product</p>
                @else
                    <ul class="featured-product-list">
                        @foreach($featured as $feature)
                            <li>
                                <img src="{{$feature->product->image}}">
                                <br>
                                <a href="/admin/cms/featured/{{$feature->id}}/edit">{{$feature->product->name}}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="footer">
                <hr>
                <a href="/admin/cms/featured/create" class="b">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    Add Featured Product
                </a>
            </div>
        </div>
        <div class="card">
            <div class="header">
                <h4 class="title">
                    Slider
                </h4>
            </div>
            <div class="content">
                @if($slider->count() == 0)
                    <p>You are not create any slide</p>
                @else
                    <ul class="cms-slider" id="lightSlider">
                        @foreach($slider as $slide)
                            <li>
                                <img src="{{$slide->image->getSrc()}}" alt="">
                                <a href="/admin/cms/slider/{{$slide->id}}/edit">Edit Slide</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="footer">
                <hr>
                <a href="/admin/cms/slider/create" class="b">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    Add Slide
                </a>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card">
            <div class="header">
                <h4 class="title">Main menu</h4>
            </div>
            <div class="content">
                @if(sizeof($nav) == 0)
                    <p>There are not any sidebar menu</p>
                @else
                    <ol class=menu-list>
                        @foreach($nav as $menu)
                            <li>
                                <a href="/admin/cms/menu/{{$menu->id}}/edit">{{$menu->text}}</a>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
            <div class="footer">
                <hr>
                <a href="/admin/cms/menu/create?type=nav" class="b">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    Add Main menu
                </a>
            </div>
        </div>
        <div class="card">
            <div class="header">
                <h4 class="title">Footer menu</h4>
            </div>
            <div class="content">
                @if(sizeof($footer) == 0)
                    <p>There are not any footer menu</p>
                @else
                    <ol class=menu-list>
                        @foreach($footer as $menu)
                            <li>
                                <a href="/admin/cms/menu/{{$menu->id}}/edit">{{$menu->text}}</a>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
            <div class="footer">
                <hr>
                <a href="/admin/cms/menu/create?type=footer" class="b">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    Add Footer menu
                </a>
            </div>
        </div>
        <div class="card">
            <div class="header">
                <h4 class="title">Page</h4>
            </div>
            <div class="content">
                @if(sizeof($pages) == 0)
                    <p>There are not any page created</p>
                @else
                    <ol class=menu-list>
                        @foreach($pages as $page)
                            <li>
                                <a href="/admin/cms/page/{{$page->id}}/edit">{{$page->page_slug}}</a>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
            <div class="footer">
                <hr>
                <a href="/admin/cms/page/create" class="b">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    Create new Page
                </a>
            </div>
        </div>
    </div>
@endsection
