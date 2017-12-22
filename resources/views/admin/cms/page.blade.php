@extends('layouts.admin')

@section('page-direction')
    <a href="/admin/cms">CMS</a> / Page
@endsection

@section('cms-sidebar')
    active
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="header">
                <h4 class="title">{{isset($content) ? 'Edit' : 'Create'}} Page</h4>
            </div>
            <div class="content">
                <div class="row">
                    <form class="col-md-12" action="/admin/cms/page{{isset($content) ? '/'.$content[0]->id : ''}}" method="post"  enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>Wrapper Size</label>
                            <select name="page_content_size" class="form-control">
                                <option value="large" {{old('page_content_size') == 'large' ? 'selected' : (isset($content) ? ($content[0]->page_content_size == 'large' ? 'selected' : '') : '' )}}>Large</option>
                                <option value="small" {{old('page_content_size') == 'small' ? 'selected' : (isset($content) ? ($content[0]->page_content_size == 'small' ? 'selected' : '') : '' )}}>Small</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Page slug</label>
                            <input type="text" name="page_slug" class="form-control" value="{{old('page_slug') ? old('page_slug') : (isset($content) ? $content[0]->page_slug : '' )}}">
                        </div>

                        <div class="form-group">
                            <label>Page title</label>
                            <input type="text" name="page_title" class="form-control" value="{{old('page_title') ? old('page_title') : (isset($content) ? $content[0]->page_title : '' )}}">
                        </div>

                        <div class="form-group">
                            <label>Page Content</label>
                            <textarea name="page_content" class="form-control ckeditor" id="ckeditro">{!!old('page_content') ? old('page_content') : (isset($content) ? $content[0]->page_content : '')!!}</textarea>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label>SEO title</label>
                            <input type="text" name="seo_title" class="form-control" value="{{old('seo_title') ? old('seo_title') : (isset($content) ? $content[0]->seo_title : '')}}">
                        </div>

                        <div class="form-group">
                            <label>SEO description</label>
                            <textarea name="seo_description" class="form-control" rows="4">{{old('seo_description') ? old('seo_description') : (isset($content) ? $content[0]->seo_description : '')}}</textarea>
                        </div>

                        <div class="form-group">
                            <label>SEO keyword</label>
                            <input type="text" name="seo_keyword" class="form-control" value="{{old('seo_keyword') ? old('seo_keyword') : (isset($content) ? $content[0]->seo_keyword : '')}}">
                        </div>

                        @include('layouts.partials.alert')

                        <input type="submit" class="btn btn-primary" value="{{isset($content) ? 'Update' : 'Create'}}">
                        <a href="/admin/cms" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>
            @if(isset($content))
                <div class="footer">
                    <hr>
                    <form action="/admin/cms/page/{{$content[0]->id}}/delete" method="post">
                        {{ csrf_field() }}
                        <button class="btn btn-danger">
                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                            Delete
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="/js/admin/ckeditor/ckeditor.js"></script>
@endpush
