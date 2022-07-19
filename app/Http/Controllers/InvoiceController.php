<?php

namespace App\Http\Controllers;

use Session;
use App\Models\Item;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\InvoiceItem;
use App\Models\InvoiceTotal;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\InvoiceHistory;
use App\Models\InvoicePayment;
use App\Exports\InvoicesExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    private $invoiceId;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->export)
            return $this->doExport($request);
        $invoices = $this->filter($request)->paginate(10)->withQueryString();
        return view('invoices.index',compact('invoices'));
    }

    /**
     * Performs exporting
     *
     * @param Request $request
     * @return void
     */
    private function doExport(Request $request)
    {
        return Excel::download(new InvoicesExport($request), 'invoices.xlsx');
    }

    private function filter(Request $request)
    {
        $query = Invoice::with('customer:id,name')->latest();
        if ($request->invoice_number)
            $query->where('invoice_number', 'like', $request->invoice_number.'%');
        if($request->amount)
            $query->where('amount', 'like', $request->amount.'%');
        if($request->invoiced_at)
            $query->where('invoiced_at', 'like', $request->invoiced_at.'%');

        return $query;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = Customer::where('enabled', 1)->orderBy('name')->pluck('name', 'id');
        $items = Item::where('enabled', 1)->orderBy('name')->pluck('name', 'id');
        $invoice_number_next = Invoice::with('customer:id,name')->latest();
        $number = $this->getNextInvoiceNumber();
        return view('invoices.create', compact('customers', 'items','number'));
    }

    public function generateItemData(Request $request)
    {
        $this->validate($request,[
            'itemId' => 'required'
        ]);
        $item = Item::where('enabled', 1)->where('id', $request->itemId)->first();
        if($item) {
            $response['status']  = '1';
            $response['quantity'] = 1;
        } else {
            $response['status']  = '0';
            $response['quantity'] = 0;
        }
        return $response;
    }

    public function getItems(Request $request)
    {
        $q = $request->q;
        $q_a = explode('_', $request->item_array);

        $data = Item::where(function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%')
                      ->orWhere('sku', 'like', '%' . $q . '%');
        })
        ->whereNotIn('id', $q_a)
        ->get();
        return response()->json($data);
    }



    /**
     * Generate next invoice number
     *
     * @return string
     */
    public function getNextInvoiceNumber()
    {
        $prefix = "INV-";
        $next = rand(1,10000);
        $digit = "5";
        $number = $prefix . str_pad($next, $digit, '0', STR_PAD_LEFT);
        return $number;
    }

    /**
     * Increase the next invoice number
     */
    public function increaseNextInvoiceNumber($company)
    {
        $currentInvoice = $company->invoice_number_next;
        $next = $currentInvoice + 1;

        DB::table('settings')->where('company_id', $company->id)
                ->where('key', 'general.invoice_number_next')
                ->update(['value' => $next]);
    }

        /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validation($request);

        $customerInfo = Customer::findOrFail($request->customer_id);
        $data = $request->only(['invoice_number','order_number','invoiced_at','due_at']);
        $data['invoice_status_code'] = 'draft';
        $data['amount'] = $request->grand_total;
        $data['customer_id'] = $request->customer_id;
        $data['customer_name'] = $customerInfo->name;
        $data['customer_email'] = $customerInfo->email;
        $data['customer_tax_number'] = $customerInfo->tax_number;
        $data['customer_phone'] = $customerInfo->phone;
        $data['customer_adress'] = $customerInfo->address;
        $data['parent_id'] = auth()->user()->id;
        $data['notes'] = $request->description;
        if ($request->picture) {
            $data['attachment'] = $request->picture->store('invoice');
        }

        DB::transaction(function () use ($data , $request) {
            $invoice = Invoice::create($data);
            $this->invoiceId = $invoice->id;
            $sub_total = 0;
            if($request->product) {
                $order_row_id = $keys = $request->product['order_row_id'];
                $oquantity = $request->product['order_quantity'];
                foreach ($keys as $id => $key) {
                    $order_quantity = (double) $oquantity[$id];
                    $item = Item::where('id', $order_row_id[$id])->first();
                    $item_sku = '';
                    $item_id = !empty($item->id) ? $item->id : 0;
                    $item_amount = (double) $item->sale_price * (double) $order_quantity;
                    if (!empty($item_id)) {
                        $item_object = Item::find($item_id);
                        $item_sku = $item_object->sku;
                        // Decrease stock (item sold)
                        $item_object->quantity -= (double) $order_quantity;
                        $item_object->save();

                    } elseif ($item->sku) {
                        $item_sku = $item->sku;
                    }

                    $invoice_item = InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'item_id' => $item_id,
                        'name' => Str::limit($item->name, 180, ''),
                        'sku' => $item_sku,
                        'quantity' => (double) $order_quantity,
                        'price' => (double) $item->sale_price,
                        'total' => $item_amount,
                    ]);
                    // Calculate totals
                    $sub_total += $invoice_item->total;
                }
            }

            $s_total = $sub_total;
            // Apply discount to total
            if ($request->total_discount) {
                $s_discount = $request->total_discount;
                $s_total = $s_total - $s_discount;
            }
            $amount = $s_total;
            $invoiceData['amount'] = $amount;
            $invoice->update($invoiceData);

            // Add invoice totals
            $this->addTotals($invoice, $request, $sub_total, $request->total_discount);
            // Add invoice history
            InvoiceHistory::create([
                'invoice_id' => $invoice->id,
                'status_code' => 'draft',
                'notify' => 0,
                'description' => $invoice->invoice_number." added!",
            ]);

        });
        return redirect()->route('invoice.show', $this->invoiceId)->with('success', trans('Invoice Added Successfully'));
    }

    public function addTotals($invoice, $request, $sub_total, $discount_total)
    {
        $sort_order = 1;
        // Added invoice sub total
        InvoiceTotal::create([
            'invoice_id' => $invoice->id,
            'code' => 'sub_total',
            'name' => 'invoices.sub_total',
            'amount' => $sub_total,
            'sort_order' => $sort_order,
        ]);
        $sort_order++;
        // Added invoice discount
        if ($discount_total > 0) {
            InvoiceTotal::create([
                'invoice_id' => $invoice->id,
                'code' => 'discount',
                'name' => 'invoices.discount',
                'amount' => $discount_total,
                'sort_order' => $sort_order,
            ]);
            // This is for total
            $sub_total = $sub_total - $discount_total;
            $sort_order++;
        }
        // Added invoice total
        InvoiceTotal::create([
            'invoice_id' => $invoice->id,
            'code' => 'total',
            'name' => 'invoices.total',
            'amount' => $sub_total,
            'sort_order' => $sort_order,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        $salesMan = User::find(auth()->user()->id);
        return view('invoices.show', compact('salesMan','invoice'));
    }

    public function getAddPaymentDetails(Request $request)
    {
        $invoice = Invoice::find($request->i_id);
        $amount = $invoice->amount - $invoice->paid;
        if($invoice) {
            $output = array('payment_amount' =>  $amount);
            return json_encode($output);
        } else {
            return response()->json(['status' => 0]);
        }
    }

    public function addPaymentStore(Request $request)
    {
        $request->validate([
            'invoice_id' => ['required', 'integer'],
            'payment_date' => ['required', 'date'],
            'payment_amount' => ['required', 'numeric'],
            'description' => ['nullable', 'string', 'max:1000']
        ]);

        DB::transaction(function () use ($request) {
            $data['invoice_id'] = $request->invoice_id;
            $data['paid_at'] = $request->payment_date;
            $data['amount'] = $request->payment_amount;
            $data['description'] = $request->description;
            $data['payment_method'] = "Cash";
            $invoicePayment = InvoicePayment::create($data);
            $myInvoiceStatus = $this->invoiceStatusUpdate($request);
            $desc_amount = $invoicePayment->amount;
            $historyData = [
                'invoice_id' => $invoicePayment->invoice_id,
                'status_code' => $myInvoiceStatus,
                'notify' => '0',
                'description' => $desc_amount . ' ' . "payments",
            ];
            InvoiceHistory::create($historyData);
        });
        return response()->json(['status' => 1]);
    }

    public function invoiceStatusUpdate($request)
    {
        $request['invoice_id'] = $request->invoice_id;
        $invoice = Invoice::find($request->invoice_id);
        if ($request['payment_amount'] > $invoice->amount - $invoice->paid) {
            $invoice->invoice_status_code = 'paid';
        } elseif ($request['payment_amount'] == $invoice->amount - $invoice->paid) {
            $invoice->invoice_status_code = 'paid';
        } else {
            $invoice->invoice_status_code = 'partial';
        }
        $invoice->save();

        return $invoice->invoice_status_code;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    private function validation(Request $request, $id = 0)
    {
        $request->validate([
            'customer_id' => ['required', 'integer'],
            'invoiced_at' => ['required', 'date'],
            'due_at' => ['required', 'date'],
            'invoice_number' => ['required', 'string', 'unique:invoices,invoice_number,' . $id],
            'order_number' => ['nullable', 'string'],
            'grand_total' => ['required', 'numeric'],
            'total_discount' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string', 'max:1000'],
            'picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $request->validate([
            "product"    => "required|array",
            "product.*"  => "required",
            "product.order_row_id.*"  => "required",
            "product.order_quantity.*"  => "required",
        ]);
    }
}
