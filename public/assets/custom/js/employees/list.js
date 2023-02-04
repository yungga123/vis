let table = 'employee_table',
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


// Edit Form
$(document).on('click', '.btn-employee-edit', function () {

    $('.form :input').each(
        function(){
            var input = $(this);
            input.removeClass("is-invalid").removeClass("is-valid");
        }
    );

    $('.form small').each(
        function(){
            var input = $(this);
            input.html('');
        }
    );

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

    $('#form-employee-edit').attr('action',$(this).data('url'));
});

//Delete Data
$(document).on('click','.delete-employee',function(){

    var id = $(this).data('id');

    $('#btn-delete-employee').off('click').on('click',function(){
        route = $(this).data('url');

        $.post(route + '/' + id,function(response){
            let status = STATUS.SUCCESS,
                message = response.messages;

            if (response.success) {

                $('#modal-delete-employee').modal('hide');
                refreshDataTable($('#' + table));
                
            } else {
                status = STATUS.ERROR;
            }

            notifMsg(message, status);

        },'json');
    });
    
});

// Form Submit Validation (For Edit)
$('#form-employee-edit').submit(function(e) {
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
                $('#modal-edit-editemployee').modal('hide');
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

                console.log(response.messages);
            }

        }
    });
});