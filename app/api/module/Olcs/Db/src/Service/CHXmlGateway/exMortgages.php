<?php

// include ch xml gateway class
require_once('lib/CHXmlGateway.php');

// create instance of this class
$xmlGateway = new CHXmlGateway(); 

// get object for appropriate search
// in this case, first argument is company number 
// and second is company name
$mortgages = $xmlGateway->getMortgages('01775733', 'Sun Microsystems');

// you can set optional values
/*
$mortgages->setStartDate('2000-01-01');
$mortgages->setEndDate('2009-01-01');
$mortgages->setSatisfiedChargesInd(true);
 */

// call getResponse method, that takes one argument and that is request 
// we want to make. In this case it is companyDetails object
echo $xmlGateway->getResponse($mortgages);
