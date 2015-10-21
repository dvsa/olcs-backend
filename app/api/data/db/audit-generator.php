<?php

ini_set('memory_limit', -1);

$users = [1, 2, 3, 4, 5, 6, 7, 8, 19, 20, 21, 22, 23, 24, 25, 12504, 12505];

$applications = [1, 2, 6, 3, 7, 8];

$output = 'TRUNCATE TABLE `application_read_audit`;';

$rows = [];

$keys = [];

for ($i = 0; $i < 9000000; $i++) {

    $user = $users[array_rand($users)];
    $application = $applications[array_rand($applications)];
    $date = date('Y-m-d', rand(strtotime('-50 year'), time()));

    if (isset($keys[$user . '-' . $application . '-' . $date])) {

        $skip = true;

        foreach ($users as $user) {
            if (!isset($keys[$user . '-' . $application . '-' . $date])) {
                $skip = false;
                break;
            }
        }

        if ($skip) {
            continue;
        }
    }

    $keys[$user . '-' . $application . '-' . $date] = '1';

    $rows[] = sprintf(
        '(%s, %s, \'%s\')',
        $user,
        $application,
        $date
    );

    if ($i % 10000 == 0) {
        $output .= 'INSERT INTO `application_read_audit` (`user_id`, `application_id`, `created_on`) VALUES ' . implode(",\n", $rows) . ";\n";
        $rows = [];
    }
}

$output .= 'INSERT INTO `application_read_audit` (`user_id`, `application_id`, `created_on`) VALUES ' . implode(",\n", $rows) . ";\n";

file_put_contents(__DIR__ . '/create_audit_test.sql', $output);
