<script>
"use strict";
$(document).ready(function() {
    "use strict";

    var quill = new Quill('#input_address', {
        theme: 'snow'
    });

    var address = $("#address").val();
    quill.clipboard.dangerouslyPasteHTML(address);
    quill.root.blur();
    $('#input_address').on('keyup', function(){
        var input_address = quill.container.firstChild.innerHTML;
        $("#address").val(input_address);
    });
});
</script>
