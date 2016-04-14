<?php

$gb = include(__DIR__ . '/en_GB.php');

$ni = array_merge(
    $gb,
    [
        'application-review-financial-history-insolvencyConfirmation' => 'Confirm that you are aware that you must tell
            the DOE immediately of any insolvency proceedings that occur between the submission of your application and
            a decision being made on the application',
        'application-review-licence-history-public-inquiry-question' => 'Has anyone you’ve named in this application (including partners, directors and Transport Managers) ever attended a Public Inquiry before a Traffic Commissioner?',
        'application-review-licence-history-disqualified-question' => 'Has anyone you’ve named in this application (including partners, directors and Transport Managers) been disqualified from holding or obtaining an operator’s licence by any Traffic Commissioner?',
        'application-review-convictions-penalties-question' => 'Has anyone you’ve named in this application (including partners, directors and Transport Managers); any company of which anyone named in this application is or has been a director; any parent company (if you are a limited company); or any of your employees or agents, ever been convicted of any relevant offence which must be declared to the Traffic Commissioner?',
        'application-review-convictions-penalties-confirmation' => 'Confirm that you are aware you must tell the Traffic Commissioner immediately of any relevant convictions that occur between the submission of your application and a decision being made on this application.',
        'tm-review-return-address' => 'Department of the Environment , The Central Licensing Office, PO Box 180 '.
            'Leeds, LS9 1BU',
    ]
);

return $ni;
