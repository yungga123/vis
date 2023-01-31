$(document).ready(function() {
    const form = $('#form_mail_config');

    form.on('submit', function(e) {
        e.preventDefault();

        if (confirm('Do you really want to continue?')) {
            const self = $(this);
            const route = self.attr('action');
            const data = self.serialize();

            showLoading();

            $.post(route, data)
                .then((res) => {
                    const message = res.errors ?? res.message,
                        elems = [
                            'email_name',
                            'email',
                            'password',
                            'oauth_client_id',
                            'oauth_client_secret',
                            'oauth_scope',
                            'redirect_uri',
                            'hostname',
                            'access_type',
                            'recepients',
                        ];

                    if (res.status !== STATUS.ERROR) {
                        notifMsg(message, res.status);
                    }

                    closeLoading();
                    showAlertInForm(elems, message, res.status);
                })
                .catch(err => catchErrMsg(err));
        }
    });
});