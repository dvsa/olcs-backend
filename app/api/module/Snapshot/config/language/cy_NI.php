<?php

$gb = include(__DIR__ . '/cy_GB.php');

$ni = array_merge(
    $gb,
    [
        'application-review-financial-history-insolvencyConfirmation' => '{WELSH} Confirm that you are aware that you
            must tell the DfI immediately of any insolvency proceedings that occur between the submission of your
            application and a decision being made on the application',
        'application-review-licence-history-public-inquiry-question' => '{WELSH} Has any person named in the application
            (including partners, directors or transport managers) ever attended a Public Inquiry before the DfI or a GB
            traffic commissioner?',
        'application-review-licence-history-disqualified-question' => '{WELSH} Has any person named in the application
            (including partners, directors or transport managers) been disqualified from holding or obtaining an
            operator\'s licence by DfI or a GB traffic commissioner?',
        'application-review-convictions-penalties-question' => '{WELSH} Has any person named in this application,
            (including partners, directors and transport managers); any company of which a person named on this
            application is or has been a director; any parent company if you are a limited company; received any
            penalties or have currently any unspent convictions?',
        'application-review-convictions-penalties-confirmation' => '{WELSH} Confirm that you are aware that you must
            tell the Department immediately of any relevant convictions that occur between the submission of your
            application and a decision being made on this application',
        'tm-review-return-address-snapshot' => 'Department for Infrastructure, Y Swyddfa Drwyddedu Ganolog, PO Box 180 '.
            'Leeds, LS9 1BU',
    ]
);

return $ni;
