@extends('layouts.layout')
@section('one_page_js')
    <script src="{{ asset('plugins/custom/js/quill.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert2/swal.js') }}"></script>
    <script src="{{ asset('plugins/flatpickr/flatpickr.js') }}"></script>
@endsection
@section('one_page_css')
    <link href="{{ asset('plugins/custom/css/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
@endsection
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('invoice.index') }}">@lang('Invoices List')</a></li>
                    <li class="breadcrumb-item active">@lang('Show Invoice')</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<img class="d-none" src="/pre-loader/loading.gif" alt="hidden-img">
<div class="progress-bar" id="progress-bar"></div>
<div class="row">
    <div class="col-12">
        @php
            switch ($invoice->invoice_status_code) {
                case 'paid':
                    $badge = 'success';
                    break;
                case 'delete':
                    $badge = 'danger';
                    break;
                case 'partial':
                case 'sent':
                    $badge = 'warning';
                    break;
                default:
                    $badge = 'primary';
                    break;
            }
        @endphp
        <div id="print-area" class="invoice p-3 mb-3 card card-{{$badge}} card-outline">
            <div class="row">
                <div class="ribbon-wrapper ribbon-lg">
                    <div class="ribbon bg-{{$badge}}">{{Str::ucfirst($invoice->invoice_status_code) }}</div>
                </div>
                <div class="col-12 ">
                    <h4><i class="fas fa-globe"></i> MIT PARK</h4>
                </div>
            </div>
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    From
                    <address>
                        <strong>{{ $salesMan->name}}</strong><br>
                        {{ "Bogura Trade Center" }}<br>
                        Phone: 09638800400<br>
                        Email: contact@masumaitpark.com
                    </address>
                </div>
                <div class="col-sm-4 invoice-col">
                    To
                    <address>
                        <strong>{{ $invoice->customer_name}}</strong><br>
                        @if ($invoice->customer_address)
                        {{ strip_tags($invoice->customer_address) }}<br>
                        @endif
                        @if ($invoice->customer_phone)
                        Phone: {{ $invoice->customer_phone }}<br>
                        @endif
                        @if ($invoice->customer_email)
                        Email: {{ $invoice->customer_email }}
                        @endif
                    </address>
                </div>
                <div class="col-sm-4 invoice-col">
                    <b>Invoice #{{$invoice->invoice_number}}</b><br>
                    <br>
                    <b>Order Number:</b> {{$invoice->order_number}}<br>
                    <b>Invoice Date:</b> {{ date("d M Y", strtotime($invoice->invoiced_at)) }}<br>
                    <b>Payment Due:</b> {{ date("d M Y", strtotime($invoice->due_at)) }}
                </div>
            </div>
            <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->total }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                </div>
                <div class="col-6">
                    <div class="table-responsive">
                        <table class="table">
                            @foreach ($invoice->totals as $total)
                                @php
                                    $totalName = explode(".",$total->name);
                                    $countNameArray = count($totalName);
                                    if($countNameArray == '1') {
                                        $name = $totalName[0];
                                    } else {
                                        $explodeWithunder = explode("_",$totalName[1]);
                                        $name = ucwords(implode(" ",$explodeWithunder));
                                    }
                                @endphp
                                @if ($total->code != 'total')
                                    <tr>
                                        <th style="width:50%">{{ $name }}:</th>
                                        <td>{{ $total->amount }}</td>
                                    </tr>
                                @else
                                    @if ($invoice->paid)
                                        <tr>
                                            <th class="text-success" style="width:50%">@lang('Paid')</th>
                                            <td>-{{ $invoice->paid }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th style="width:50%">{{ $name }}:</th>
                                        <td>{{ $total->amount - $invoice->paid }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <div class="row no-print">
                <div class="col-12">
                    <a href="#" data-href="{{ route('invoice.destroy', $invoice) }}" class="btn btn-outline-danger btn-lg float-right btn-lg" data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-trash ambitious-padding-btn"></i> Delete
                    </a>

                    <a class="btn btn-outline-warning btn-lg float-right" style="margin-right: 5px;" href="{{ route('invoice.index') }}">
                        <i class="fas fa-ban"></i> Cancel
                    </a>

                    @if ($invoice->invoice_status_code != 'paid')
                        <a class="btn btn-lg btn-outline-success" id="addPaymentModel" i_id="{{$invoice->id}}" href="javascript:void(0)"> <i class="fas fa-money-check-alt mr-2"></i>Add Payment</a>
                    @endif

                    <button type="button" id="doPrint" class="btn btn-lg btn-outline-info" style="margin-right: 5px;">
                        <i class="fas fa-print"></i> Print
                    </button>

                    <a href="{{ route('invoice.edit', $invoice) }}" class="btn btn-outline-dark btn-lg">
                        <i class="fas fa-pen"></i> Edit
                    </a>
                </div>
            </div>
      </div>
    </div>
  </div>

    <div class="modal fade modal-print" id="addPaymentModalView" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-inline">New Payment</h5>
                    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-material form-horizontal" action="#" method="POST" enctype="multipart/form-data">
                        @method('PATCH')
                        <input type="hidden" name="invoice_id" id="invoice_id" value="{{ $invoice->id }}">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="payment_date">@lang('Date') <b class="ambitious-crimson">*</b></label>
                                <div class="form-group input-group mb-3">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text"><i class="fas fa-calendar"></i>
                                    </div>
                                    <input type="text" name="payment_date" id="payment_date" class="form-control" autofocus required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="payment_amount">@lang('Amount') <b class="ambitious-crimson">*</b></label>
                                <div class="form-group input-group mb-3">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text"><i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <input type="text" name="payment_amount" id="payment_amount" class="form-control" autofocus required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="col-md-12"><h4>@lang('Description')</h4></label>
                                <div class="col-md-12">
                                    <div id="payment_description" style="min-height: 55px;">
                                    </div>
                                    <input type="hidden" name="description" id="description">
                                </div>
                                @if ($errors->has('description'))
                                    {{ Session::flash('error',$errors->first('description')) }}
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer no-print">
                    <input id="add_payment_button" type="submit" value="@lang('Submit')" class="btn btn-primary"/>
                    <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fas fa-times"></i> @lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
    @include('script.invoice.show.js')
    @include('layouts.delete_modal')
@endsection
