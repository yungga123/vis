<script>
    $('#status option[value="30.00"]').attr('disabled',true);
        $('#status option[value="50.00"]').attr('disabled',true);
        $('#status option[value="70.00"]').attr('disabled',true);
        $('#status option[value="90.00"]').attr('disabled',true);
        $('#status option[value="100.00"]').attr('disabled',true);

        $('#quarter').val("<?= $date_quarter ?>");
        $('#quarter option').attr('disabled',true);
        $('#quarter option[value="<?= $date_quarter ?>"]').attr('disabled',false);

        $('#min_forecast_date').attr('readonly',true);
        $('#max_forecast_date').attr('readonly',true);

        $('#close_deal_date').attr('readonly',true);
        $('#project_start_date').attr('readonly',true);
        $('#project_finish_date').attr('readonly',true);

        $("#forecast_close_date").change(function(){
            min_forecast = moment($('#forecast_close_date').val()).subtract(6,'days').format('YYYY-MM-DD');
            max_forecast = moment($('#forecast_close_date').val()).add(6,'days').format('YYYY-MM-DD');
            $('#min_forecast_date').val(min_forecast);
            $('#max_forecast_date').val(max_forecast);
        });

        $("#project").prop("type", "hidden");
        $("#project_amount").prop("type", "hidden");
        $("#quotation_num").prop("type", "hidden");
        $("#close_deal_date").prop("type", "hidden");
        $("#project_start_date").prop("type", "hidden");
        $("#project_finish_date").prop("type", "hidden");
        $("#forecast_close_date").prop("type", "hidden");
        $("#min_forecast_date").prop("type", "hidden");
        $("#max_forecast_date").prop("type", "hidden");

        $('label[for="project"]').hide();
        $('label[for="project_amount"]').hide();
        $('label[for="quotation_num"]').hide();
        $('label[for="close_deal_date"]').hide();
        $('label[for="project_start_date"]').hide();
        $('label[for="project_finish_date"]').hide();
        $('label[for="forecast_close_date"]').hide();
        $('label[for="min_forecast_date"]').hide();
        $('label[for="max_forecast_date"]').hide();
</script>