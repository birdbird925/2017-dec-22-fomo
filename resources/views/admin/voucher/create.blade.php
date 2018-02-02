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
                    <form class="col-md-6" action="/admin/voucher" method="post"  enctype="multipart/form-data">
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
                              <option value=1>By Percentage</option>
                              {{-- <option value=2>Fix Amount</option>
                              <option value=3>Free Shipping</option> --}}
                            </select>
                        </div>

                        <div class="form-group" id="voucher-value-group">
                            <label for="value" class="vourcher-type-label">Percentage of discount</label>
                            <input type="number" name="discount" id="value" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="start">Start time</label>
                            <input type="text" id="start" name="start_time" class="form-control" data-toggle="datepicker">
                        </div>

                        <div class="form-group">
                            <label for="end">End time</label>
                            <input type="text" id="end" name="end_time" class="form-control" data-toggle="datepicker">
                        </div>

                        <div class="form-group">
                            <label for="quantity">Dispatch Quantity</label>
                            <input id="quantity" type="number" name="quantity" class="form-control" min=1 value="1">
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

@push('scripts')
  <script>
    $('#type').on('change', function(){
      switch($(this).val()) {
        case '1':
          $('#voucher-value-group').css("display", "block");
          $('.vourcher-type-label').html('Percentage of discount');
          break;

        case '2':
          $('#voucher-value-group').css("display", "block");
          $('.vourcher-type-label').html('Fix amount of discount');
          break;

        case '3':
          $('#voucher-value-group').css("display", "none");
          break;
      }
    });
    $('[data-toggle="datepicker"]').datepicker();
  </script>
@endpush
