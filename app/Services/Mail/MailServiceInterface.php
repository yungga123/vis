<?php

namespace App\Services\Mail;

interface MailServiceInterface
{
    /**
     * Runs the mail sending process.
     *
     * @param array  $data    The array of data to send.
     * @param string|null $module_code   The module code to determine the module.
     */
    public function send(array $data, ?string $module_code = null): void;
}