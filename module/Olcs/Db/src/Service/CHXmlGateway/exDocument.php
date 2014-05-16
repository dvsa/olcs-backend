<?php

// include ch xml gateway class
require_once('lib/CHXmlGateway.php');

// create instance of this class
$xmlGateway = new CHXmlGateway();

// get object for appropriate search
// in this case, argument is company number
$document = $xmlGateway->getDocument('01775733');

// you can set optional values
// $companyDetails->setMortTotals(true);

// call getResponse method, that takes one argument and that is request 
// we want to make. In this case it is companyDetails object
echo $xmlGateway->getResponse($document);
