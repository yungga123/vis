<script>
    <?php foreach ($validation as $field => $message) { ?>
        
        $('#small_<?= $field ?>').html('<?= $message ?>');
            
    <?php } ?>

    $("#forecast_close_date").change(function(){
        min_forecast = moment($('#forecast_close_date').val()).subtract(6,'days').format('YYYY-MM-DD');
        max_forecast = moment($('#forecast_close_date').val()).add(6,'days').format('YYYY-MM-DD');
        $('#min_forecast_date').val(min_forecast);
        $('#max_forecast_date').val(max_forecast);
    });

    $('#quarter').val("<?= $date_quarter ?>");
    $('#quarter option').attr('disabled',true);
    $('#quarter option[value="<?= $date_quarter ?>"]').attr('disabled',false);
</script>

