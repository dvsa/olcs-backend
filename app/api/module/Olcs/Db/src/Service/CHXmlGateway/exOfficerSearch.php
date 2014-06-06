<?php

// include ch xml gateway class
require_once('lib/CHXmlGateway.php');

// create instance of this class
$xmlGateway = new CHXmlGateway();

// get object for appropriate search
// in this case, first argument is officer surname
// and second is officer type
$officer = $xmlGateway->getOfficerSearch('Graham', 'CUR');

// you can set optional values
// call getResponse method, that takes one argument and that is request
// we want to make. In this case it is officer object
echo $xmlGateway->getResponse($officer);
