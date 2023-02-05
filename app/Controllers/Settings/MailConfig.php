<?php

namespace App\Controllers\Settings;

use App\Controllers\BaseController;
use App\Models\MailConfigModel;
use League\OAuth2\Client\Provider\Google;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class MailConfig extends BaseController
{
    /**
     * For displaying the view
     *
     * @return view
     */
    public function index()
    {
        $model = new MailConfigModel();

        $data['title']          = 'Settings | Mail Configuration';
        $data['page_title']     = 'Settings | Mail Configuration';
        $data['custom_js']      = 'settings/mail_config.js';
        $data['sweetalert2']    = true;
        $data['mail']           = $model->getMailConfig();

        return view('settings/send_mail', $data);
    }

    /**
     * For saving changes
     *
     * @return json_encode array
     */
    public function save()
    {
        $data = [];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model = new MailConfigModel();

            $data['status'] = STATUS_SUCCESS;
            $data['message'] = "Changes have been saved!";

            if (!$model->save($this->request->getVar())) {
                $data['errors'] = $model->errors();
                $data['status'] = STATUS_ERROR;
                $data['message'] = "Validation error!";
            } else {
                log_message(
                    'error',
                    'Mail config data has been saved. Updated by {username} with details ({employee_id}, {access_level}) at {saved_at} from {ip_address}.',
                    [
                        'username' => session()->get('username'),
                        'employee_id' => session()->get('employee_id'),
                        'access_level' => session()->get('access_level'),
                        'saved_at' => date('Y-m-d H:i:s'),
                        'ip_address' => $this->request->getIPAddress(),
                    ]
                );
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception$e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status'] = STATUS_ERROR;
            // $data['errors']     = $e->getMessage();
            $data['message'] = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * Function for sending mail using PHPMailer library
     *
     * @param array $params
     * @param string $sendVia
     * @return array
     */
    protected function send(array $params, $sendVia = 'xoauth2')
    {
        $error = '';

        //Create a new PHPMailer instance
        $mail = new PHPMailer();

        try {
            // $this->checkIfStillHasSessionToken();

            //Get mail config details
            $model = new MailConfigModel();
            $mail_config = $model->getMailConfig();

            if (empty($mail_config)) {
                exit('There is no mail config data.');
            }

            //Tell PHPMailer to use SMTP
            $mail->isSMTP();

            //Enable SMTP debugging
            //SMTP::DEBUG_OFF = off (for production use)
            //SMTP::DEBUG_CLIENT = client messages
            //SMTP::DEBUG_SERVER = client and server messages
            $mail->SMTPDebug = SMTP::DEBUG_OFF;

            //Set the hostname of the mail server
            $mail->Host = $mail_config['hostname'];

            //Set the SMTP port number:
            // - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
            // - 587 for SMTP+STARTTLS
            $mail->Port = 587;

            //Set the encryption mechanism to use:
            // - SMTPS (implicit TLS on port 465) or
            // - STARTTLS (explicit TLS on port 587)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            //Whether to use SMTP authentication
            $mail->SMTPAuth = true;

            if ($sendVia === 'xoauth2') {
                $this->_sendViaOAuth2($mail, $mail_config);
            } else {
                $this->_sendViaRegularSMPT($mail, $mail_config);
            }

            //Set who the message is to be sent from
            $mail->setFrom($mail_config['email'], $mail_config['email_name']);

            //Set who will receive the mail
            $mail->addAddress($params['email_address'], $params['employee_name'] ?? '');

            //Set who can get a copy
            if (!empty($mail_config['recepients'])) {
                $split = explode(',', $mail_config['recepients']);
                foreach ($split as $val) {
                    $mail->addCC(trim($val));
                }

            }
            // $mail->addBCC('bcc@example.com');

            //Set the subject line
            $mail->Subject = $params['subject'] ?? $mail_config['email_name'];

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Set email format to HTML
            $mail->isHTML(true);

            //Content
            $mail->Body = $params['body'] ?? $this->_bodyMailTemplate($params);

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->msgHTML($mail->Body);

            //send the message, check for errors
            if (!$mail->send()) {
                throw new Exception("Mail could not be sent!", 1);
            }
        } catch (Exception $e) {
            $error = 'Mail could not be sent! Please contact your system administrator.';
            log_message(
                'error',
                'Mail could not be sent. Mailer Error: {mail_error}! [ERROR] {exception}!',
                ['mail_error' => $mail->ErrorInfo, 'exception' => $e]
            );
        }

        return [
            'status' => empty($error) ? STATUS_SUCCESS : STATUS_ERROR,
            'message' => empty($error) ? ' A mail has been sent to employee.' : $error,
        ];
    }

    /**
     * Configure and get access token for OAuth2
     *
     * @param boolean $view
     * @return mixed
     */
    public function config($view = true)
    {
        $this->reset();

        $model = new MailConfigModel();
        $mail_config = $model->getMailConfig();
        $session = session();
        $params = [
            'clientId' => $mail_config['oauth_client_id'],
            'clientSecret' => $mail_config['oauth_client_secret'],
            'redirectUri' => $mail_config['redirect_uri'],
            'accessType' => $mail_config['access_type'],
        ];
        $provider = new Google($params);
        $options = [
            'scope' => [$mail_config['oauth_scope']],
        ];

        //Check if oauth2state is in session
        if (!isset($_SESSION['oauth2state']) && isset($_GET['state'])) {
            $session->set(['oauth2state' => $_GET['state']]);
        }

        if (null === $provider) {
            // exit('Provider missing');
            log_message('error', 'OAuth2: Provider missing!');
            return $this->_status(STATUS_ERROR, $view);
        }

        if (!isset($_GET['code'])) {
            //If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl($options);
            // Set the session for oauth2state
            $session->set(['oauth2state' => $provider->getState()]);

            if (!isset($_SESSION['oauth2state'])) {
                exit('Session is not set.');
            }

            header('Location: ' . $authUrl);
            exit;

            //Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            // State is invalid, possible CSRF attack in progress
            unset($_SESSION['oauth2state']);
            // exit('Invalid state');

            log_message('error', 'OAuth2: Invalid state!');
            return $this->_status(STATUS_ERROR, $view);
        } else {
            //Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken(
                'authorization_code',
                ['code' => $_GET['code']]
            );
            //Use this to interact with an API on the users behalf
            // $token->getToken();

            //Use this to get a new access token if the old one expires
            // $token->getRefreshToken();

            // Set session for the token
            $session->set(['token' => serialize($token)]);
            // Set session for the refresh token
            $session->set(['refresh_token' => $token->getRefreshToken()]);

            // var_dump('token', $token); echo '<br>';
            // var_dump('getRefreshToken', $token->getRefreshToken());

            $model->saveRefreshToken($token->getRefreshToken());

            return $this->_status(STATUS_SUCCESS, $view);
        }
    }

    /**
     * Remove or clear any session of the OAuth2
     *
     * @return view / redirect
     */
    public function reset()
    {
        unset($_SESSION['token'], $_SESSION['state']);
        unset($_SESSION['oauth2state']);
        unset($_SESSION['refresh_token']);

        return redirect()->route('mail.home');
    }

    /**
     * Get session token and unserialize it
     *
     * @return mixed
     */
    public function getSessionToken()
    {
        if (isset($_SESSION['token']) && !empty($_SESSION['token'])) {
            return unserialize($_SESSION['token']);
        }

        return null;
    }

    /**
     * Check if still has session token and not expired
     *
     * @return void
     */
    public function checkIfStillHasSessionToken()
    {
        if (isset($_SESSION['token']) && !empty($_SESSION['token'])) {
            $token = unserialize($_SESSION['token']);

            if ($token->hasExpired()) {
                $this->config();
                // throw new Exception("Mail could not be sent! OAuth2 token has exprired.", 1);
            }
        }

        $this->config();
    }

    /**
     * Send mail via xoauth2
     *
     * @param [object] $mail
     * @param [array] $mail_config
     * @return void
     */
    private function _sendViaOAuth2($mail, $mail_config)
    {
        //Set AuthType to use XOAUTH2
        $mail->AuthType = 'XOAUTH2';

        $refreshToken = $mail_config['refresh_token'];
        // if ($token = $this->getSessionToken() && empty($refreshToken)) {
        // $token = $this->getSessionToken();
        // $refreshToken = $token->getToken();
        // }

        // echo $refreshToken;

        //Create a new OAuth2 provider instance
        $provider = new Google([
            'clientId' => $mail_config['oauth_client_id'],
            'clientSecret' => $mail_config['oauth_client_secret'],
        ]);

        //Pass the OAuth provider instance to PHPMailer
        $mail->setOAuth(
            new OAuth([
                'provider' => $provider,
                'clientId' => $mail_config['oauth_client_id'],
                'clientSecret' => $mail_config['oauth_client_secret'],
                'refreshToken' => $refreshToken,
                'userName' => $mail_config['email_name'],
            ])
        );
    }

    /**
     * Send via regular smpt
     *
     * @param [object] $mail
     * @param [array] $mail_config
     * @return void
     */
    private function _sendViaRegularSMPT($mail, $mail_config)
    {
        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = $mail_config['email'];

        //Password to use for SMTP authentication
        $mail->Password = $mail_config['password'];
    }

    /**
     * Body template of mail
     *
     * @param array $params
     * @return string|html elements
     */
    public function _bodyMailTemplate(array $params)
    {
        $name = ucfirst($params['employee_name']);
        $body = "<p>Hi {$name},</p>";

        if (isset($params['is_add'])) {
            $body .= "
                <p>
                    Your account has been created for <b>Vinculum MIS</b>! You can now login using the credentials below:
                </p>
            ";
        } else {
            $body .= "
                <p>
                    Your password has been changed. You can now login using the credentials below:
                </p>
            ";
        }

        $login = site_url('/login');
        $body .= "
            Username: {$params['username']} <br>
            Password: {$params['password']} <br>
            Link: {$login} <br>

            <p>
                Regards, <br>
                Vinculum MIS<br>
                <small><i>[This is auto generated. Please don't reply to this email!]</i></small>
            </p>
        ";

        return $body;
    }

    /**
     * View status of OAuth2 configuration
     *
     * @param string $status
     * @param boolean $view
     * @return view|array
     */
    public function _status($status = 'success', $view = true)
    {
        $message = 'Your OAuth2 Google Client configuration is success.';
        if ($status === STATUS_ERROR) {
            $status = 'danger';
            $message = "There's an error in your OAuth2 Google Client configuration";
        }

        $data['title'] = 'Status of OAuth Configuration';
        $data['page_title'] = 'Status of OAuth Configuration';
        $data['status'] = $status;
        $data['message'] = $message;

        if ($view === false) {
            return [
                'status' => $status === 'success' ? $status : 'error',
                'message' => $message,
            ];
        }

        return view('settings/status', $data);
    }
}
