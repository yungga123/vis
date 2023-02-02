$(document).ready(function() {
    const form = $('#form_mail_config');

    passwordShowHideInit();

    $('#btn_getAccessToken').on('click', function() {
        if (
            isEmpty($('#oauth_client_id').val()) ||
            isEmpty($('#oauth_client_secret').val()) ||
            isEmpty($('#oauth_scope').val()) ||
            isEmpty($('#redirect_uri').val())
        ) {
            notifMsg('OAuth2 Google Client fields are required except the "Access Type"!', STATUS.ERROR);

            return false;
        }

        if (confirm('Do you really want to continue?')) return true;

        return false;
    });

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