<?php

// Define your own en language response message
return [
    'success' => [
        'added'     => '{title} has been added!',
        'saved'     => '{title} has been saved!',
        'updated'   => '{title} has been updated!',
        'deleted'   => '{title} has been deleted!',
        'fetched'   => '{title} has been retrieved!',
        'retrieved' => '{title} has been retrieved!',
        'changed'   => '{title} has been {status}!',
        'uploaded'  => '{title} has been uploaded!',
        'removed'   => '{title} has been removed!',
    ],
    'error' => [
        'validation'    => 'Validation error!',
        'process'       => 'Error while processing data! Please contact your system administrator!',
        'email'         => 'Field must contain a valid email!',
    ],
    'status' => [
        'success'   => 'success',
        'info'      => 'info',
        'error'     => 'error',
    ],
    'restrict' => [
        'permission' => [
            'add'       => "You don't have permission to <strong>ADD</strong> a record. Kindly ask the permission first!",
            'change'    => "You don't have permission to <strong>{action}</strong> a record. Kindly ask the permission first!",
        ],
        'action' => [
            'change'    => "No more changes are allowed for this record!",
        ],
    ],
];
