<script>
    "use strict";

    $(document).ready(function() {

        $('.dropify').dropify();

        var quill = new Quill('#input_description', {
            theme: 'snow'
        });
        quill.root.innerHTML = $('#description').val();
        quill.root.blur();

        $(document).on('submit', '#itemQuickForm', function(e){
            $('#description').val(quill.container.firstChild.innerHTML);
        });

        $(".select2").select2();
    });
</script>
