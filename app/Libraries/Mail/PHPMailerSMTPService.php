<?php

namespace App\Libraries\Mail;

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class PHPMailerSMTPService extends MailContent
{
    /**
     * The mail hostname.
     *
     * @var string
     */
    protected $hostname = '';

    /**
     * The mail port. Default tls
     *
     * @var string
     */
    protected $port = 587;

    /**
     * The mail charset. Default utf-8
     *
     * @var string
     */
    protected $charset = 'utf';

    /**
     * Send the mail.
     *
     * @return void
     */
    public function send()
    {
        // Check the authentication first
        $this->checkAuthentication();

        // Load default config if there's no hostname set
        if (empty($this->hostname)) $this->defaultConfig();

        // Check if mail was sent, otherwise throw an exception
        if (! parent::send()) {
            throw new Exception("Mail could not be sent!", 1);
        }
    }

    /**
     * SMTP Authentication.
     *
     * @param  string  $username The email address
     * @param  string  $password
     * @param  string  $encryption (tls or ssl)
     * @return void
     */
    public function authenticate($username, $password, $encryption = 'tls')
    {
        $this->isSMTP();
        $this->SMTPAuth = true;
        $this->Username = $username;
        $this->Password = $password;

        // Implicit TLS on port 465 or
        // Explicit TLS on port 587
        $this->SMTPSecure = $encryption;
    }

    /**
     * Set the config hostname (like smtp.gmail.com), port [587, 465], charset [ascii, iso or utf]) to use.
     *
     * @param  string  $hostname
     * @param  int  $port
     * @param  string  $encryption
     * @return void
     */
    public function config($hostname, $port = 0, $charset = 'utf')
    {
        // The hostname of the server
        $this->Host = $hostname;

        // 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
        // 587 for SMTP+STARTTLS
        $this->Port = $port ? $port : $this->port;

        // Set the charset
        $this->CharSet = $this->charset($charset);
    }

    /**
     * Set the debugging mode [off, client or server] (default - off).
     *
     * @param  string  $charset
     * @return void
     */
    public function debug($debug = 'off')
    {
        $debugs = [
            'off'       => SMTP::DEBUG_OFF,     //off (for production use)
            'client'    => SMTP::DEBUG_CLIENT,  //client messages
            'server'    => SMTP::DEBUG_SERVER,  //client and server messages
        ];

        $this->SMTPDebug = $debugs[$debug];
    }

    /**
     * Defualt config (hostname, port and charset).
     *
     * @return void
     */
    protected function defaultConfig()
    {
        // The hostname of the server
        $this->Host = 'smtp.gmail.com';

        // 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
        // 587 for SMTP+STARTTLS
        $this->Port = $this->port;

        // Set the charset
        $this->CharSet = $this->charset($this->charset);
    }

    /**
     * Check the authentication.
     *
     * @return void
     */
    protected function checkAuthentication()
    {
        if (!$this->SMTPAuth || !$this->Username || !$this->Password) {
            throw new Exception(
                'You must set the SMTP Username & Password to send mail! Pass the arguments to '. get_class($this) .'::authenticate() method.',
                1
            );
        }
    }
}