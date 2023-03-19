var table,form_upload;

$(document).ready(function () {
	table = "tasklead_booked_table";
	form_upload = "form_upload";

	const route = $("#" + table).data("url"),
		options = {
			columnDefs: {
				targets: [4, 5],
				orderable: false,
			},
		};

	loadDataTable(table, route, METHOD.POST, options);

	
	$('#'+form_upload).on('submit', function(e){
		e.preventDefault();
		let id = $('#upload_id').val();
		//let file = $('#project_file').val();
		let self = $(this);
		let url = self.attr('action');
		$('#tasklead_id').val(id);

		let formData = new FormData(this);    


		$.ajax({
			url: url,
			type: METHOD.POST,
			data: formData,
			dataType: "json",
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {
				if (response.success == true){
					//console.log('success');
					notifMsgSwal("Success!",response.message,STATUS.SUCCESS);
					getTaskleadFiles(id);
					self[0].reset();
					$('#modal-addfile').modal('hide');
				} else {
					notifMsgSwal("Error!",response.errors,STATUS.ERROR);
				}
			},
		});
	})
});

function getBookedDetails(id){
	let url = $('#booked_details_url').val() + "?tasklead_id="+id;
	let history_url = $('#booked_history_url').val() + "?tasklead_id="+id;

	$.post(
		url,
		{ id : id },
		function(res){
			//console.log(res);
			$.each(res, function(key,value){
				//console.log('value:'+value.id);
				// use value.(table_name)
				$('#upload_id').val(id);
				$('.project_amount').html(value.project_amount);
				$('.project_start_date').html(value.project_start_date);
				$('.project_finish_date').html(value.project_finish_date);
				$('.project').html(value.project);
				$('.customer_name').html(value.customer_name);
				$('.branch_name').html(value.branch_name);
				$('.contact_number').html(value.contact_number);
				$('.quotation_num').html(value.quotation_num);
				$('.forecast_close_date').html(value.forecast_close_date);
				$('.min_forecast_date').html(value.min_forecast_date);
				$('.max_forecast_date').html(value.max_forecast_date);
				$('.status1').html(value.status1);
				$('.employee_name').html(value.employee_name);
				getTaskleadFiles(id);
			});
		}
	);

	$.post(
		history_url,
		{ id : id },
		function(res){
			$.each(res, function(key, value){
				
				if(value.status=='10.00%'){
					$('.history_created_at').html('Created On: '+value.created_at);
					$('.rns_10').html('Remark Next Step: '+value.remark_next_step);
				}

				if(value.status=='30.00%'){
					$('.history_project').html('Project: '+value.project);
					$('.rns_30').html('Remark Next Step: '+value.remark_next_step);
				}

				if(value.status=='50.00%'){
					$('.history_quotation_num').html('Quotation Number: '+value.quotation_num);
					$('.rns_50').html('Remark Next Step: '+value.remark_next_step);
					$('.history_project_amount').html('Project Amount: '+value.project_amount);
					$('.history_forecast_close_date').html('Forecast Close Date: '+value.forecast_close_date);
					$('.history_min_forecast_date').html('Min Forecast Date: '+value.min_forecast_date);
					$('.history_max_forecast_date').html('Max Forecast Date: '+value.max_forecast_date);
					
				}

				if(value.status=='70.00%'){
					$('.rns_70').html('Remark Next Step: '+value.remark_next_step);
					
					
				}

				if(value.status=='90.00%'){
					$('.history_project_amount2').html('Project Amount: '+value.project_amount);
					$('.rns_90').html('Remark Next Step: '+value.remark_next_step);
				}

				if(value.status=='100.00%'){
					$('.rns_100').html('Remark Next Step: '+value.remark_next_step);
					$('.history_close_deal_date').html('Close Deal Date: '+value.close_deal_date);
					$('.history_project_start_date').html('Start Date: '+value.project_start_date);
					$('.history_project_finish_date').html('End Date: '+value.project_finish_date);
					$('.history_project_duration').html('Project Duration: '+value.project_duration);
				}
				
			});
		}
	);

}

function getTaskleadFiles(id) {
	let url = $('#booked_files_url').val();
	$.post(url,
		{id:id},
		function(response){
			//console.log(response.map);
			// <li>
			// 	<a href="<?= base_url('uploads/project-booked/' . $id . '/' . $item); ?>" class="btn-link text-secondary"><i class="far fa-fw fa-file-word"></i> </a>
			// </li>
			$('.files').empty();
			$.each(response.map,function(key,value){
				//console.log(value);
				
				$('.files').append("<li><a href='"+$('#download_url').val()+"?id="+$('#upload_id').val()+"&file="+value+"'class='btn-link text-secondary'><i class='far fa-fw fa-file-word'></i> "+value+"</a></li>");
			});
		});
}

function downloadFile(id) {
	url = $('#download_url').val();
	$.post(
		url,
		{id:id},
		function(response){
			notifMsgSwal("Success!",response.message,STATUS.SUCCESS);
		}
	);
}
