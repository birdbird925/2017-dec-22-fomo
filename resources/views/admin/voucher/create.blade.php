@extends('layouts.admin')

@section('page-direction')
    <a href="/admin/voucher">Voucher</a> / New voucher
@endsection

@section('voucher-sidebar')
    active
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="header">
                <h4 class="title">New discount voucher</h4>
            </div>
            <div class="content">
                <div class="row">
                    <form class="col-md-6" action="/admin/cms/featured{{isset($content) ? '/'.$content->id : ''}}" method="post"  enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="name">Voucher Name</label>
                            <input id="name" type="text" name="name" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="code">Voucher Code</label>
                            <input id="code" type="text" name="code" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="type">Voucher Type</label>
                            <select name="type" id="type" class="form-control">
                              <option value="1">By Percentage</option>
                              <option value="2">Fix Amount</option>
                              <option value="3">Free Shipping</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="value" class="vourcher-type-label">Percentage of discount</label>
                            <input type="number" id="value" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="start_at">Start time</label>
                            <input type="text" id="start_at" name="start_at" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="end_at">End time</label>
                            <input type="text" id="end_at" name="expired_at" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="quantity">Dispatch Quantity</label>
                            <input id="quantity" type="number" class="form-control" min=1 value="1">
                        </div>

                        @include('layouts.partials.alert')

                        <input type="submit" class="btn btn-primary" value="{{isset($content) ? 'Update' : 'Create'}}">
                        <a href="/admin/cms" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
