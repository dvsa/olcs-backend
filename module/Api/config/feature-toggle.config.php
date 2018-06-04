<?php

/**
 * POC for feature toggle configs - we'll move this into the DB or Redis at a later date
 */
$active = 'always-active';
$inactive = 'inactive';
$conditional = 'conditionally-active';

return [
    'Something descriptive' => [
        'name' => 'e.g. TestHandler::class',
        'conditions' => [],
        'status' => $active
    ],
];
