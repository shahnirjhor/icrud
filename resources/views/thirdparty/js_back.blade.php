<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('plugins/alertifyjs/alertify.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>

<script>
    function selectChange(val) {
        $('#myForm').submit();
    }

    $(document).on('click', '#doPrint', function(){
        var printContent = $('#print-area').html();
        $('body').html(printContent);
        window.print();
        location.reload();
    });

    $(document).on('click', '#doDownload', function(){
        var printContent = $('#print-area').html();
        var file = $('body').html(printContent).download();
        var filename = "invoice.pdf";
        download(filename, file);
    });
</script>
