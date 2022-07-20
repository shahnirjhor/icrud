@extends('layouts.layout')
@section('one_page_js')
    <script src="{{ asset('plugins/flatpickr/flatpickr.js') }}"></script>
@endsection

@section('one_page_css')
    <link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
@endsection
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3><a href="{{ route('invoice.create') }}" class="btn btn-outline btn-info">+ @lang('Add New Invoice')</a></h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">@lang('Dashboard')</a></li>
                    <li class="breadcrumb-item active">@lang('Invoice List')</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">@lang('Invoices') </h3>
                <div class="card-tools">
                    <a class="btn btn-primary" target="_blank" href="{{ route('invoice.index') }}?export=1">
                        <i class="fas fa-cloud-download-alt"></i> @lang('Export')
                    </a>
                    <button class="btn btn-default" data-toggle="collapse" href="#filter">
                        <i class="fas fa-filter"></i> @lang('Filter')
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="filter" class="collapse @if(request()->isFilterActive) show @endif">
                    <div class="card-body border">
                        <form action="" method="get" role="form" autocomplete="off">
                            <input type="hidden" name="isFilterActive" value="true">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>@lang('Invoice Number')</label>
                                        <input type="text" name="invoice_number" class="form-control" value="{{ request()->invoice_number }}" placeholder="@lang('Invoice Number')">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>@lang('Invoice Date')</label>
                                        <input type="text" name="invoiced_at" class="form-control flatpickr" value="{{ request()->invoiced_at }}" placeholder="@lang('Invoice Date')">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>@lang('Amount')</label>
                                        <input type="text" name="amount" class="form-control" value="{{ request()->amount }}" placeholder="@lang('Amount')">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-info">@lang('Submit')</button>
                                    @if(request()->isFilterActive)
                                        <a href="{{ route('invoice.index') }}" class="btn btn-secondary">@lang('Clear')</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <table id="laravel_datatable" class="table table-striped compact table-width">
                    <thead>
                        <tr>
                            <th>@lang('Invoice No')</th>
                            <th>@lang('Customer')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Invoice Date')</th>
                            <th>@lang('Due Date')</th>
                            <th>@lang('Status')</th>
                            <th data-orderable="false">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->customer->name }}</td>
                            <td>{{ $invoice->amount }}</td>
                            <td>{{ date("d M Y", strtotime($invoice->invoiced_at)) }}</td>
                            <td>{{ date("d M Y", strtotime($invoice->due_at)) }}</td>
                            <td>
                                @php
                                    switch ($invoice->invoice_status_code) {
                                        case 'paid':
                                            $badge = 'badge badge-success';
                                            break;
                                        case 'delete':
                                            $badge = 'badge badge-danger';
                                            break;
                                        case 'partial':
                                        case 'sent':
                                            $badge = 'badge badge-warning';
                                            break;
                                        default:
                                            $badge = 'badge badge-primary';
                                            break;
                                    }
                                @endphp
                                    <span class="{{$badge}}">{{Str::ucfirst($invoice->invoice_status_code) }}</span>

                            </td>
                            <td>
                                <a target="_blank" href="{{ route('invoice.download', ['id' => $invoice]) }}" class="btn btn-info btn-outline btn-circle btn-lg" data-toggle="tooltip" title="Download"><i class="fa fa-download ambitious-padding-btn"></i></a>&nbsp;&nbsp;
                                <a href="{{ route('invoice.show', $invoice) }}" class="btn btn-info btn-outline btn-circle btn-lg" data-toggle="tooltip" title="View"><i class="fa fa-eye ambitious-padding-btn"></i></a>&nbsp;&nbsp;
                                <a href="{{ route('invoice.edit', $invoice) }}" class="btn btn-info btn-outline btn-circle btn-lg" data-toggle="tooltip" title="Edit"><i class="fa fa-edit ambitious-padding-btn"></i></a>&nbsp;&nbsp;
                                <a href="#" data-href="{{ route('invoice.destroy', $invoice) }}" class="btn btn-info btn-outline btn-circle btn-lg" data-toggle="modal" data-target="#myModal" title="Delete"><i class="fa fa-trash ambitious-padding-btn"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
@include('layouts.delete_modal')

@include('script.invoice.index.js')
@endsection
