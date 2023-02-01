let table = 'project_list_table',
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