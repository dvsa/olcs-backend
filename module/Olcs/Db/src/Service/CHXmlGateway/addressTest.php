<?php

require_once('lib/core/countyForPostcode.php');

$countyForPostcode = new countyForPostcode();

$result = $countyForPostcode->getCouncil('WV164AW');

echo $result['administrative']['council']['title'];