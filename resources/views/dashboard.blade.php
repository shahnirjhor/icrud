@extends('layouts.layout')

@section('one_page_css')
    <link href="{{ asset('plugins/custom/css/dashboard.css') }}" rel="stylesheet">
@endsection
@section('one_page_js')
<script src="{{ asset('plugins/bower_components/chart.js/bundle.js') }}"></script>
<script src="{{ asset('plugins/bower_components/chart.js/utils.js') }}"></script>
@endsection
@section('content')
<div class="container-fluid">
    <br>
    <div class="row">
        <!---Income-->
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-blue"><i class="fas fa-hand-holding-usd"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('Total customers')</span>
                </div>
            </div>
        </div>

        <!---Expense-->
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-sitemap"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">@lang('Total Items')</span>
                </div>
            </div>
        </div>

        <!---Profit-->
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-shopping-cart"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">@lang('Total Invoice')</span>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <h4 class="box-title">@lang('Latest Invoice')</h4>
            <table class="table table-striped compact table-width table-bordered">
                <thead>
                    <tr class="table-success">
                        <th>@lang('Date')</th>
                        <th>@lang('Categories')</th>
                        <th class="text-right">@lang('Amount')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="13">
                            <h5 class="text-center">@lang('No Records')</h5>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <h4 class="box-title">@lang('Latest Item')</h4>
            <table class="table table-striped compact table-width table-bordered">
                <thead>
                    <tr class="table-danger">
                        <th>@lang('Date')</th>
                        <th>@lang('Categories')</th>
                        <th class="text-right">@lang('Amount')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="13">
                            <h5 class="text-center">@lang('No Records')</h5>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@include('script.dashboard.view.js')

@endsection
