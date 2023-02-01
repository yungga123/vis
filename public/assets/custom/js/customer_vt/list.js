

$(document).ready(function () {
    let table = 'customer_vt',
        route = $('#' + table).data('url');

    /* Disable sorting for this column - default is 1st column. 
    1 = 2nd column of the table  */
    let options = {
        columnDefs: {
            targets: 1,
            orderable: false
        },
    };

    /* Load dataTable */
    loadDataTable(table, route, METHOD.POST, options);


    $(document).on('click', '.btn-customer-edit', function () {
        $.ajax({
            url: $(this).data('url'),
            type: 'get',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {

                $.each(response,function(key,value){
                    $('#' + key).val(value);
                });
            }
        });

        $('#form-editcustomervt').attr('action',$(this).data('url'));
    });

    $('#form-editcustomervt').submit(function(e) {
        e.preventDefault();
        var me = $(this);
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "2000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        $.ajax({
            url: me.attr('action'),
            type: 'post',
            data: me.serialize(),
            dataType: 'json',
            success: function(response) {
    
                $('.form :input').each(
                    function(){
                        var input = $(this);
                        input.removeClass("is-invalid").addClass("is-valid");
                    }
                );
                $('.form small').each(
                    function(){
                        var input = $(this);
                        input.html('');
                    }
                );
                
                if (response.success == true) {
                    toastr.success("Successfully Edited!");

                    $('#modal-edit-customervt').modal('hide')

                    refreshDataTable();
    
                    me[0].reset();

                    $('.form :input').each(
                        function(){
                            var input = $(this);
                            input.removeClass("is-invalid").removeClass("is-valid");
                        }
                    );

                } else {
    
                    toastr.error("Errors Occured!");
    
                    $.each(response.messages, function(key, value) {
                        if (value != '') {
                            $('#' + key).removeClass("is-valid").addClass("is-invalid");
                            $('#small_' + key).html(value);
                        }
                    });
                }
    
            }
        });
    });



});