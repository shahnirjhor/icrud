<script>
    "use strict";
    $(document).ready( function () {
        $('#laravel_datatable').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
        });

        $(".flatpickr").flatpickr({
            enableTime: false
        });
    });
</script>
