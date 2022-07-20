<table>
    <thead>
        <tr>
            <th>Invoice Number</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Invoice Data</th>
            <th>Due Date</th>
            <th>Status</th>

        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
        <tr>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->customer_name }}</td>
            <td>{{ $invoice->amount }}</td>
            <td>{{ date($companyDateFormat, strtotime($invoice->invoiced_at)) }}</td>
            <td>{{ date($companyDateFormat, strtotime($invoice->due_at)) }}</td>
            <td>{{ $invoice->invoice_status_code }}</td>

        </tr>
        @endforeach
    </tbody>
</table>
