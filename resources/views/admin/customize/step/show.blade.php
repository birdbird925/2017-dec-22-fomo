@extends('layouts.admin')

@section('page-direction')
    <a href="/admin/customize/step">Step</a> / {{$step->title}}
@endsection

@section('customize-sidebar')
    active
@endsection

@section('customize-dropdown')
    in
@endsection

@section('step-sidebar')
    active
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Edit Customize Step</h4>
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-sm-6">
                        <form action="/admin/customize/step/{{$step->id}}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Main Title</label>
                                <input type="text" name="main_title" class="form-control" value="{{$step->title}}">
                            </div>
                            <div class="form-group">
                                <label>Sub Title</label>
                                <input type="text" name="sub_title" class="form-control" value="{{$step->extral_title}}">
                            </div>
                            @include('layouts.partials.alert')
                            <input type="submit" class="btn btn-primary" value="Update">
                            <a href="/admin/customize/step" class="btn btn-default">Cancel</a>
                        </form>
                    </div>
                    <div class="col-sm-5 col-sm-offset-1">
                        <div class="note-wrapper">
                            <div class="note-title">
                                Step Selection
                            </div>
                            @if(!$step->primary && $step->componentByLevel(1)->count() <= 1)
                                This step has not provide user any selection.
                            @else
                                <ol>
                                    @if($step->primary)
                                        @foreach($types as $type)
                                            <li>
                                                <a href="/admin/customize/type/{{$type->id}}">
                                                    {{$type->name}}
                                                </a>
                                            </li>
                                        @endforeach
                                    @else
                                        @foreach($step->componentByLevel(1) as $component)
                                            <li>
                                                <a href="/admin/customize/component/{{$component->id}}">
                                                    @if($component->type != 'image')
                                                        {{$component->value}}
                                                    @else
                                                        <img src="{{$component->image('value')->getSrc()}}" width="120px" style="border: 1px solid #F2F2F2">
                                                    @endif
                                                </a>
                                                @if($component->type_id)
                                                    <span class="pull-right">{{$component->customizeType->name}}</span>
                                                @endif
                                            </li>
                                        @endforeach

                                        @if($step->componentByLevel(2)->count() > 0)
                                            </ol>
                                            <br>
                                            <div class="note-title">{{$step->componentByLevel(2)->first()->level_title}}</div>
                                            <ol>
                                            @foreach($step->componentByLevel(2) as $component)
                                                <li>
                                                    <a href="/admin/customize/component/{{$component->id}}">
                                                        @if($component->type != 'image')
                                                            {{$component->value}}
                                                        @else
                                                            <img src="{{$component->image('value')->getSrc()}}" width="120px" style="border: 1px solid #F2F2F2">
                                                        @endif
                                                    </a>
                                                    @if($component->type_id)
                                                        <span class="pull-right">{{$component->customizeType->name}}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        @endif
                                    @endif
                                </ol>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
