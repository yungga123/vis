<?php

namespace App\Libraries\Mail;

use PHPMailer\PHPMailer\PHPMailer;

class MailContent extends PHPMailer
{
    /**
     * Set the sender for the mail.
     *
     * @param  string  $address
     * @param  string  $name
     * @return $this
     */
    public function from($address, $name = '')
    {
        $this->setFrom($address, $name);
        
        return $this;
    }

    /**
     * Set the receiver for the mail.
     *
     * @param  string  $address
     * @param  string  $name
     * @return $this
     */
    public function to($address, $name = '')
    {
        $this->addAddress($address, $name);

        return $this;
    }

    /**
     * Set the receiver for the mail carbon copy.
     *
     * @param  string|array  $address
     * @return $this
     */
    public function cc($address)
    {
        $this->checkParam($address);

        if (is_array($address)) {
            foreach ($address as $val) $this->addCC($val);
        } else {
            $this->addCC($address);
        }

        return $this;
    }

    /**
     * Set the receiver for the mail blind carbon copy.
     *
     * @param  string|array  $address
     * @return $this
     */
    public function bcc($address)
    {
        $this->checkParam($address);

        if (is_array($address)) {
            foreach ($address as $val) $this->addBCC($val);
        } else {
            $this->addCC($address);
        }

        return $this;
    }

    /**
     * Set the subject for the mail.
     *
     * @param  string $subject
     * @return $this
     */
    public function subject($subject)
    {
        $this->Subject = $subject;

        return $this;
    }

    /**
     * Set the body for the mail.
     *
     * @param  string  $body
     * @return $this
     */
    public function body($body, $isHTML = false)
    {
        $this->Body = $body;

        if ($isHTML || has_html_tags($body)) {
            $this->isHTML($isHTML);
            $this->msgHTML($this->Body);
        }

        return $this;
    }

    /**
     * Attach the file/s to the mail.
     *
     * @param  string|array  $files
     * @param  string  $name
     * @return $this
     */
    public function attach($files, $name = '')
    {
        $this->checkParam($files);

        if (is_array($files)) {
            foreach ($files as $val) $this->addAttachment($val);
        } else {
            $this->addAttachment($files, $name);
        }

        return $this;
    }

    /**
     * Set the charsets [ascii, iso or utf] (default - utf).
     *
     * @param  string  $charset
     * @return string
     */
    protected function charset($charset = 'utf')
    {
        $charsets = [
            'ascii' => parent::CHARSET_ASCII,
            'iso'   => parent::CHARSET_ISO88591,
            'utf'   => parent::CHARSET_UTF8,
        ];

        return $charsets[$charset];
    }

    /**
     * Check if param is empty.
     *
     * @param  string|array  $param
     * @return $this
     */
    protected function checkParam($param)
    {
        if (empty($param)) return $this;
    }
}