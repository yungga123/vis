<script>
    $('#customer_id option').attr('disabled',true);

    $('#customer_id option[value="<?= $selectedCustomer ?>"]').attr('disabled',false).attr('selected',true);
</script>