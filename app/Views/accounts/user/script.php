<script type="text/javascript">
    $(document).ready(function() {
        const form = $('#form_user');

        form.on('submit', function(e) {
            e.preventDefault();

            if (confirm('Do you really want to continue?')) {
                const self = $(this);
                const route = '<?= url_to('user.change_pass'); ?>';
                const data = self.serialize();

                $.post(route, data)
                    .then((res) => {
                        let message = res.message;
                        let multiple = false;

                        if ('errors' in res) {
                            message = res.errors;
                            multiple = true;
                        }

                        if (res.status === STATUS.SUCCESS) {
                            self[0].reset();

                            setTimeout(() => {
                                window.location.href = '<?= url_to('logout'); ?>';
                            }, 4000);
                        }

                        notifMsg(message, res.status, multiple);
                    })
                    .catch(err => console.log(err));
            }
        });
    });
</script>