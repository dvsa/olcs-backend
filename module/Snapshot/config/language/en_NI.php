<?php

$gb = include(__DIR__ . '/en_GB.php');

$ni = array_merge(
    $gb,
    [
        'application-review-financial-history-insolvencyConfirmation' => 'Confirm that you are aware that you must tell
            the DOE immediately of any insolvency proceedings that occur between the submission of your application and
            a decision being made on the application',
        'application-review-licence-history-public-inquiry-question' => 'Has any person named in the application
            (including partners, directors or transport managers) ever attended a Public Inquiry before the DOE or a GB
            traffic commissioner?',
        'application-review-licence-history-disqualified-question' => 'Has any person named in the application
            (including partners, directors or transport managers) been disqualified from holding or obtaining an
            operator\'s licence by DOE or a GB traffic commissioner?',
        'application-review-convictions-penalties-question' => 'Has any person named in this application, (including
            partners, directors and transport managers); any company of which a person named on this application is or
            has been a director; any parent company if you are a limited company; received any penalties or have
            currently any unspent convictions?',
        'application-review-convictions-penalties-confirmation' => 'Confirm that you are aware that you must tell the
            Department immediately of any relevant convictions that occur between the submission of your application and
            a decision being made on this application',
        'tm-review-return-address' => 'Department of the Environment , The Central Licensing Office, PO Box 180 '.
            'Leeds, LS9 1BU',
    ]
);

return $ni;
