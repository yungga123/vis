$(document).ready(function () {
    let table = 'accounts_table',
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

    $(document).on('click', '.delete-account', function () {
        var id = $(this).data('id');

        $('#btn-delete-account').on('click', function () {
            route = $(this).data('url');

            $.get(route + '/' + id, function (response) {
                let status = STATUS.SUCCESS,
                    message = response.messages;

                if (response.success == true) {
                    $('#modal-delete-account').modal('hide');
                    refreshDataTable($('#' + table));
                } else {
                    status = STATUS.ERROR;
                }

                notifMsg(message, status);
            }, 'json');
        });
    });
});