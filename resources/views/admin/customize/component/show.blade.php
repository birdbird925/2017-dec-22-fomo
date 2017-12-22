@extends('layouts.admin')

@section('page-direction')
    <a href="/admin/customize/step">Step</a> / <a href="/admin/customize/step/{{$component->step_id}}">{{$component->step->title}}</a> / {{$component->type != 'text' ? $component->type : ''}} {{$component->value}}
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
                <h4 class="title">Edit Customize Component</h4>
            </div>
            <div class="content">
                @if($component->blank)
                    <p>
                        This component represent as blank option.
                        <br>
                        Nothing required to update.
                    </p>
                    <a href="/admin/customize/step/{{$component->step_id}}" class="btn btn-default">Go Back</a>
                @elseif($component->personalize)
                    <p>
                        This component used to defined {{$component->personalize}} personalization.
                        <br>
                        Nothing required to update.
                    </p>
                    <a href="/admin/customize/step/{{$component->step_id}}" class="btn btn-default">Go Back</a>
                @else
                    <div class="row">
                        <div class="col-sm-6">
                            <form action="/admin/customize/component/{{$component->id}}" method="post">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    @if($component->type != 'image')
                                        <label>Title</label>
                                        <input type="text" name="value" value="{{$component->value}}" class="form-control">
                                    @else
                                        <label>Image</label>
                                        <input type="file" name="image-value">
                                        <br>
                                        <img src="{{$component->image('value')->getSrc()}}" width="120px" style="border: 1px solid #F2F2F2">
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control ckeditor" id="ckeditro">{!!old('description') ? old('description') : $component->description!!}</textarea>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="available" {{$component->available ? 'checked' : ''}}>
                                    <label>Available</label>
                                </div>
                                @include('layouts.partials.alert')
                                <input type="submit" class="btn btn-primary" value="Update">
                                <a href="/admin/customize/step/{{$component->step_id}}" class="btn btn-default">Cancel</a>
                            </form>
                        </div>
                        <br>
                        <div class="col-sm-5 col-sm-offset-1">
                            <div class="note-wrapper">
                                <div class="note-title">
                                    Sub Selection
                                </div>
                                @if($component->option->count() != 0)
                                    <ol>
                                        @foreach($component->option as $option)
                                            <li>
                                                <a href="/admin/customize/component/{{$component->id}}/extral/{{$option->id}}">
                                                    @if($option->type != 'image')
                                                        {{$option->value}}
                                                    @else
                                                        <img src="{{$option->image('value')->getSrc()}}" width="120px" style="border: 1px solid #F2F2F2">
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ol>
                                @else
                                    There are not any sub selection
                                @endif
                            </div>

                            <h4 class="title">Images</h4>
                            <br>
                            <div class="row">
                                <div class="form-group col-xs-6">
                                    <label>Front Image</label>
                                    <input type="file">
                                    @if($component->front_image)
                                    <br>
                                    <img src="{{$component->image('front_image')->getSrc()}}" width="120px" style="border: 1px solid #F2F2F2">
                                    @endif
                                </div>
                                <div class="form-group col-xs-6">
                                    <label>Back Image</label>
                                    <input type="file">
                                    @if($component->back_image)
                                    <br>
                                    <img src="{{$component->image('back_image')->getSrc()}}" width="120px" style="border: 1px solid #F2F2F2">
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name="size-image" {{$component->size_image ? 'checked' : ''}}>
                                <label>Variant size</label>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="/js/admin/ckeditor/ckeditor.js"></script>
@endpush
