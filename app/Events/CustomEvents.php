<?php

namespace App\Events\CustomEvents;

use CodeIgniter\Events\Events;

// Job Order mail sending event
Events::on('send_mail_notif_job_order', static function ($data) {
    $service = new \App\Services\Mail\JobOrderMailService();
    $service->send($data);
});

// Schedule mail sending event
Events::on('send_mail_notif_schedule', static function ($data) {
    $service = new \App\Services\Mail\ScheduleMailService();
    $service->send($data);
});

// Employee mail sending event
Events::on('send_mail_notif_employee', static function ($data) {
    $service = new \App\Services\Mail\EmployeeMailService();
    $service->send($data);
});

// Account mail sending event
Events::on('send_mail_notif_account', static function ($data) {
    $service = new \App\Services\Mail\AccountMailService();
    $service->send($data);
});

// TaskLead mail sending event
Events::on('send_mail_notif_tasklead', static function ($data) {
    $service = new \App\Services\Mail\TaskLeadMailService();
    $service->send($data);
});

// Prf mail sending event
Events::on('send_mail_notif_prf', static function ($data) {
    $service = new \App\Services\Mail\PrfMailService();
    $service->send($data);
});

// Rpf mail sending event
Events::on('send_mail_notif_rpf', static function ($data) {
    $service = new \App\Services\Mail\RpfMailService();
    $service->send($data);
});