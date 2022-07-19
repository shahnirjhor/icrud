<script>
    $(document).ready( function () {
        $(document.body).on('click','#addPaymentModel',function(){
            var i_id = $(this).attr('i_id');
            $("#progress-bar").show();
            $.ajax({
                url: '{{ url('invoice/getAddPaymentDetails') }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                dataType : 'JSON',
                data:{i_id:i_id},
                success:function(response){
                    $("#progress-bar").hide();
                    if(response.status == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!'
                        })
                    } else {
                        $("#payment_amount").val(response.payment_amount);
                        $("#addPaymentModalView").modal('show');
                    }
                }
            });
        });

        $(document.body).on('click','#add_payment_button',function(){
            $("#progress-bar").show();
            var itemName = "iCRUD";
            var invoice_id = $("#invoice_id").val();
            var payment_date = $("#payment_date").val();
            var payment_amount = $("#payment_amount").val();
            var description = $("#description").val();
            if(invoice_id=="") {
                $("#progress-bar").hide();
                Swal.fire(
                    itemName,
                    '{{ __('Iurchase Id Required') }}',
                    'warning'
                );
                return;
            }

            if(payment_date=="") {
                $("#progress-bar").hide();
                Swal.fire(
                    itemName,
                    '@lang('Payment Date Required')',
                    'warning'
                );
                return;
            }

            if(payment_amount=="") {
                $("#progress-bar").hide();
                Swal.fire(
                    itemName,
                    '@lang('Payment Amount Required')',
                    'warning'
                );
                return;
            }

            $.ajax({
                url: '{{ url('invoice/addPaymentStore') }}',
                type:'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data: {
                    invoice_id: invoice_id,
                    payment_date: payment_date,
                    payment_amount: payment_amount,
                    description: description
                },
                dataType:'json',
                success:function(response){
                    $("#progress-bar").hide();
                    if(response.status==0){
                        Swal.fire(
                            itemName,
                            '@lang('Oops Something Wrong')',
                            'warning'
                        ).then(function() {
                            $('#addPaymentModalView').modal('hide');
                        });
                    } else {
                        Swal.fire(
                            itemName,
                            '@lang('Payment Succussfully Added')',
                            'success'
                        ).then(function() {
                            $('#addPaymentModalView').modal('hide');
                            window.location.reload();
                        });
                    }
                }
            });
        });

        $("#payment_date").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            defaultDate: ["{{ date('Y-m-d H:i') }}"]
        });

        var equill = new Quill('#payment_description', {
            theme: 'snow'
        });
        var description = $("#description").val();
        equill.clipboard.dangerouslyPasteHTML(description);
        equill.root.blur();
        $('#payment_description').on('keyup', function(){
            var payment_description = equill.container.firstChild.innerHTML;
            $("#description").val(payment_description);
        });
    });
</script>
