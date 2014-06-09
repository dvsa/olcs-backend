<?php

// include ch xml gateway class
require_once('lib/CHXmlGateway.php');

// create instance of this class
$xmlGateway = new CHXmlGateway();

// get object for appropriate search
// in this case, first argument is partial company number and second is array with data sets
$numberSearch = $xmlGateway->getNumberSearch('*133795', array('LIVE'));

// now you can set optional paramenters if needed
$numberSearch->setSearchRows(10);

// call getResponse method, that takes one argument and that is request
// we want to make. In this case it is numberSearch object
echo $xmlGateway->getResponse($numberSearch);
