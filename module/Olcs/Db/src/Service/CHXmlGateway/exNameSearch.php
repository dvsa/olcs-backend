<?php

// include ch xml gateway class
require_once('lib/CHXmlGateway.php');

// create instance of this class
$xmlGateway = new CHXmlGateway(); 

// get object for appropriate search
// in this case, first argument is company name and second is data set
$nameSearch = $xmlGateway->getNameSearch('Sun microsystems', 'LIVE');

// now you can set optional paramenters if needed
$nameSearch->setSearchRows(10);

// call getResponse method, that takes one argument and that is request 
// we want to make. In this case it is nameSearch object
echo $xmlGateway->getResponse($nameSearch);
