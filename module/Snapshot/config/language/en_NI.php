<?php

$gb = include(__DIR__ . '/en_GB.php');

$ni = array_merge(
    $gb,
    [
        'application-review-financial-history-insolvencyConfirmation' => 'Confirm you are aware that you must tell the DfI immediately of any insolvency proceedings that occur between the submission of your application and a decision being made on the application',
        'application-review-licence-history-public-inquiry-question' => 'Has anyone you’ve named in this application (including partners, directors and Transport Managers) ever attended a Public Inquiry held by the DfI Northern Ireland or a GB Traffic Commissioner?',
        'application-review-licence-history-disqualified-question' => 'Has anyone you’ve named in this application (including partners, directors and Transport Managers) been disqualified from having an operator’s licence in Great Britain or Northern Ireland?',
        'application-review-convictions-penalties-question' => 'Has anyone you’ve named in this application (including partners, directors and Transport Managers); any company of which anyone named in this application is or has been a director; any parent company (if you are a limited company); or any of your employees or agents, ever been convicted of any relevant offence which must be declared to the Department for Infrastructure?',
        'application-review-convictions-penalties-confirmation' => 'Confirm you are aware that you must tell the DfI immediately of any relevant convictions that occur between the submission of your application and a decision being made on this application.',
        'tm-review-return-address-snapshot' => 'Department for Infrastructure, The Central Licensing Office, PO Box 180 Leeds, LS9 1BU',
    ]
);

return $ni;
