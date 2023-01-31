<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

class SendMail extends BaseController
{
    private CONST _CLIENTID = '78826786970-v4ok5eokif3e4390go2l52aur79crga5.apps.googleusercontent.com';
    private CONST _CLIENTSECRET = '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';

    public function index()
    {
        // $this->initialize();
        // print_r(session()->get());

        if (! empty(session()->get('refresh_token'))) {
            // $this->sendMail();   
        } else {
            echo 'Referesh token is empty';
        }
    }

    public function initialize($with_echo = false)
    {
        $clientId     = '78826786970-v4ok5eokif3e4390go2l52aur79crga5.apps.googleusercontent.com';
        $clientSecret = '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';
        // $redirectUri  = 'http://localhost:8080/';
        // $redirectUri  = 'http://localhost:8080/email/send-email';

        //If this automatic URL doesn't work, set it yourself manually to the URL of this script
        // $redirectUri = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        // $redirectUri  = 'http://localhost:8080/email/send-email';
        $redirectUri  = 'http://localhost:8080/';

        // if (session_status() === PHP_SESSION_NONE) {
        //     session_start();
        // }

        $session = session();

        $params = [
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUri,
            'accessType' => 'offline'
        ];

        $provider = new Google($params);
        $options = [
            'scope' => [
                'https://mail.google.com/'
            ]
        ];

        if (null === $provider) {
            exit('Provider missing');
        }
        
        if (!isset($_GET['code'])) {
            //If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl($options);
            // $_SESSION['oauth2state'] = $provider->getState();
            $session->set(['oauth2state' => $provider->getState()]);
            if (! isset($_SESSION['oauth2state'])) {
                exit('Session is not set.');
            }

            header('Location: ' . $authUrl);
            exit;

            //Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
          
            exit('Invalid state');
        } else {
            //Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken(
                'authorization_code',
                [
                    'code' => $_GET['code']
                ]
            );
            //Use this to interact with an API on the users behalf
            //Use this to get a new access token if the old one expires
            $session->set(['token' => serialize($token)]);
            $session->set(['refresh_token' => $token->getRefreshToken()]);

            
            if ($with_echo) {
                echo 'token session: ', $session->get('token');
                echo '<br>Refresh Token session: ', $session->get('refresh_token');
                echo '<br>Refresh Token: ', $token->getRefreshToken();
            }
            // return $token->getRefreshToken();
        }

        // return null;
    }

    public function sendMail1() 
    { 
        try {
            //Create a new PHPMailer instance
            $mail = new PHPMailer();

            //Tell PHPMailer to use SMTP
            $mail->isSMTP();

            //Enable SMTP debugging
            //SMTP::DEBUG_OFF = off (for production use)
            //SMTP::DEBUG_CLIENT = client messages
            //SMTP::DEBUG_SERVER = client and server messages
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;

            //Set the hostname of the mail server
            $mail->Host = 'smtp.gmail.com';

            //Set the SMTP port number:
            // - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
            // - 587 for SMTP+STARTTLS
            // $mail->Port = 465;
            $mail->Port = 587;

            //Set the encryption mechanism to use:
            // - SMTPS (implicit TLS on port 465) or
            // - STARTTLS (explicit TLS on port 587)
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            //Whether to use SMTP authentication
            $mail->SMTPAuth = true;

            //Set AuthType to use XOAUTH2
            $mail->AuthType = 'XOAUTH2';

            // if (! isset($_SESSION['refresh_token'])) {
            //     $this->initialize();
            // }

            // $refreshToken = session()->get('refresh_token');
            // echo $refreshToken.'<br>';

            $refreshToken = '1//0exzjw-sZqm8tCgYIARAAGA4SNwF-L9IrHoXmcU3SP-Zqyynn6E59_Xg_J541duZtHXW4P-PmZwXJuxSTV3FCdOE-sLQa5yGpoQ4';

            //Create a new OAuth2 provider instance
            $provider = new Google(
                [
                    'clientId' => self::_CLIENTID,
                    'clientSecret' => self::_CLIENTSECRET,
                ]
            );

            //Pass the OAuth provider instance to PHPMailer
            $mail->setOAuth(
                new OAuth(
                    [
                        'provider' => $provider,
                        'clientId' => self::_CLIENTID,
                        'clientSecret' => self::_CLIENTSECRET,
                        'refreshToken' => $refreshToken,
                        'userName' => getenv('GMAIL_EMAIL'),
                    ]
                )
            );

            //Recipients
            $mail->setFrom(getenv('GMAIL_EMAIL'), getenv('GMAIL_NAME'));
            // $mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
            $mail->addAddress('radyballs69@gmail.com');               //Name is optional
            $mail->addReplyTo(getenv('GMAIL_EMAIL'), getenv('GMAIL_HOST'));
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');
        
            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
        
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->msgHTML($mail->Body);

            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            //send the message, check for errors
            if (!$mail->send()) {
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
                echo 'Message sent!';
            }
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        // $to = 'seo.sakamotoproperties@gmail.com';
        // $subject = 'Test Subject Email';
        // $message = 'Test Body Email';
        
        // $email = \Config\Services::email();
        // $email->setTo($to);
        // // $email->setFrom('johndoe@gmail.com', 'Confirm Registration');
        
        // $email->setSubject($subject);
        // $email->setMessage($message);

        // if ($email->send()) 
		// {
        //     echo 'Email successfully sent';
        // } 
		// else 
		// {
        //     $data = $email->printDebugger(['headers']);
        //     print_r($data);
        // }
    }

    public function reset()
    {
        unset($_SESSION['token'], $_SESSION['state']);
        unset($_SESSION['oauth2state']);
        unset($_SESSION['refresh_token']);

        $session = session();

        $session->destroy();

        // return redirect()->to('/');
    }
}
