<?php

namespace unit\OlcsCommon\Controller;

use \OlcsCommon\Controller\AbstractHttpControllerTestCase;

class CompaniesHouseControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp($noConfig = false)
    {
        $this->setApplicationConfig(
            include __DIR__.'/../../../../../../../config/test/application.config.php'
        );
        parent::setUp();
    }
    
    /**
     * Method to test the api controller for the nameSearch action of the controller
     * 
     */
    public function testNameSearchActionCanBeAccessed()
    {
        $this->dispatch('/api/companieshouse/namesearch/s');

        $this->assertModuleName('OlcsCommon');
        $this->assertControllerName('olcscommon\api\companieshouse');
        $this->assertControllerClass('CompaniesHouseController');
        $this->assertActionName('nameSearch');
        $this->assertMatchedRouteName('common_api/name_search');
    }
    
    /**
     * Method to test the api controller for the numberSearch action of the controller
     * 
     */
    public function testNumberSearchActionCanBeAccessed()
    {
        $this->dispatch('/api/companieshouse/numbersearch/6701883');

        $this->assertModuleName('OlcsCommon');
        $this->assertControllerName('olcscommon\api\companieshouse');
        $this->assertControllerClass('CompaniesHouseController');
        $this->assertActionName('numberSearch');
        $this->assertMatchedRouteName('common_api/number_search');
    }
    
    /**
     * Method to test the api controller for the numberSearch action of the controller
     * 
     */
    public function testCompanyDetailsActionCanBeAccessed()
    {
        $this->dispatch('/api/companieshouse/companydetails/06701883');

        $this->assertModuleName('OlcsCommon');
        $this->assertControllerName('olcscommon\api\companieshouse');
        $this->assertControllerClass('CompaniesHouseController');
        $this->assertActionName('companydetails');
        $this->assertMatchedRouteName('common_api/company_details');
    }

}