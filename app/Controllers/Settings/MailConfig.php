<?php

namespace App\Controllers\Settings;

use App\Controllers\BaseController;
use App\Models\MailConfigModel;
use App\Models\MailNotifModel;
use League\OAuth2\Client\Provider\Google;
use PHPMailer\PHPMailer\OAuth;

class MailConfig extends BaseController
{
    /**
     * Use to initialize model class
     * @var object
     */
    private $_model;

    /**
     * Use to initialize model class
     * @var object
     */
    private $_mailNotifModel;

    /**
     * Use to get current module code
     * @var string
     */
    private $_module_code;
    
    /**
     * Use to get current permissions
     * @var array
     */
    private $_permissions;

    /**
     * Use to check if can save
     * @var bool
     */
    private $_can_save;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_model           = new MailConfigModel(); // Current model
        $this->_mailNotifModel  = new MailNotifModel(); // Current model
        $this->_module_code     = MODULE_CODES['mail_config']; // Current module
        $this->_permissions     = $this->getSpecificPermissions($this->_module_code);
        $this->_can_save        = $this->checkPermissions($this->_permissions, ACTION_SAVE);
    }

    /**
     * Display the account view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code, ACTION_VIEW);

        $mail_notifs = $this->_mailNotifModel->getMailNotifs(
            null, '',
            function($builder) {
                if (! is_developer()) 
                    $builder->where('is_mail_notif_enabled != 0');
            }
        );
        $mail_notifs = flatten_array($mail_notifs, 'module_code');

        $data['title']          = 'Settings | Mail Configuration';
        $data['page_title']     = 'Settings | Mail Configuration';
        $data['custom_js']      = 'settings/mail_config.js';
        $data['can_save']       = $this->_can_save;
        $data['mail']           = $this->_model->getMailConfig();
        $data['mail_notifs']    = $mail_notifs;
        $data['modules']        = get_modules();
        $data['sweetalert2']    = true;
        $data['toastr']         = true;
        $data['bootstrap_switch'] = true;
        $data['routes']         = json_encode([
            'mail_config' => [
                'save'      => url_to('mail_config.save'),
            ],
        ]);

        return view('settings/mail_config/index', $data);
    }   

    /**
     * For saving changes
     *
     * @return json
     */
    public function save()
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.saved', 'Changes')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->checkRoleActionPermissions($this->_module_code, ACTION_SAVE, true);

                $inputs = $this->request->getVar();
                $model  = $this->_model;
                $param  = 'config';

                if (isset($inputs['module_code'])) {
                    $_recipients    = trim(trim($inputs['cc_recipients']), ', ');
                    $param          = 'notifs';
                    $inputs         = [
                        'module_code'           => $inputs['module_code'],
                        'has_mail_notif'        => $inputs['has_mail_notif'],
                        'is_mail_notif_enabled' => $inputs['is_mail_notif_enabled'],
                        'cc_recipients'         => $_recipients,
                        'updated_by'            => session('username'),
                    ];
                    if (! empty($_recipients)) {
                        $recipients = ['cc_recipients' => clean_param(explode(',', $_recipients))];
                        $validate   = $this->validateData(
                            $recipients,
                            ['cc_recipients.*' => 'permit_empty|valid_email|max_length[500]']
                        );

                        if (! $validate) {
                            $data['errors']     = ['recipients' => res_lang('error.email')];
                            $data['status']     = res_lang('status.error');
                            $data['message']    = res_lang('error.validation');
        
                            return $data;
                        }
                        $data['recipients'] = $_recipients;
                    }

                    $model  = new MailNotifModel();
                    $save   = $model->upsert($inputs);
                } else {
                    $inputs['is_enable'] = (isset($inputs['is_enable']) && $inputs['is_enable']) ? $inputs['is_enable'] : 'NO';
                    $save   = $model->save($inputs);
                }

                if (! $save) {
                    if (empty($model->errors())) return $data;

                    $data['errors']     = $model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    log_msg(
                        "Mail {$param} data has been saved. Updated by {username} with details ({employee_id}, {access_level}) at {saved_at} from {ip_address}.",
                        [
                            'username'      => session('username'),
                            'employee_id'   => session()->get('employee_id'),
                            'access_level'  => session()->get('access_level'),
                            'saved_at'      => date('Y-m-d H:i:s'),
                            'ip_address'    => $this->request->getIPAddress(),
                        ]
                    );
                }

                return $data;
            }
        );

        return $response;
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

        $mail_config = $this->_model->getMailConfig();
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
            return $this->_status(res_lang('status.error'), $view);
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
            return $this->_status(res_lang('status.error'), $view);
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

            $this->_model->saveRefreshToken($token->getRefreshToken());

            return $this->_status(res_lang('status.success'), $view);
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

        return redirect()->route('mail_config.home');
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
        if ($status === res_lang('status.error')) {
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
