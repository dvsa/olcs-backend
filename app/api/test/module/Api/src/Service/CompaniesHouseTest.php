<?php

/**
 * Tests the companies house service
 *
 * @note migrated from OlcsTest\Db\Service
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Api\Service;

use OlcsTest\Bootstrap;
use PHPUnit_Framework_TestCase;
use Dvsa\Olcs\Api\Service\CompaniesHouseService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Tests the companies house service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CompaniesHouseServiceTest extends MockeryTestCase
{
    /**
     * Setup the service
     *
     * @param array $methods
     */
    public function setUpService($methods = array())
    {
        return $this->getMock('Dvsa\Olcs\Api\Service\CompaniesHouseService', $methods);
    }

    /**
     * Test the getNewGateway method
     */
    public function testGetNewGateway()
    {
        $service = new CompaniesHouseService();

        $gateway = $service->getNewGateway();

        $this->assertInstanceOf('CompaniesHouse\CHXmlGateway', $gateway);
    }

    /**
     * Test getList without type
     */
    public function testGetListWithoutType()
    {
        $expected = array('Count' => 0, 'Results' => array());

        $data = array(
            'value' => 'foo'
        );

        $service = new CompaniesHouseService();

        $results = $service->getList($data);

        $this->assertEquals($expected, $results);
    }

    /**
     * Test getList without value
     */
    public function testGetListWithoutValue()
    {
        $expected = array('Count' => 0, 'Results' => array());

        $data = array(
            'type' => 'foo'
        );

        $service = new CompaniesHouseService();

        $results = $service->getList($data);

        $this->assertEquals($expected, $results);
    }

    /**
     * Test getList without value or type
     */
    public function testGetListWithoutValueOrType()
    {
        $expected = array('Count' => 0, 'Results' => array());

        $data = array();

        $service = new CompaniesHouseService();

        $results = $service->getList($data);

        $this->assertEquals($expected, $results);
    }

    /**
     * Test getList with incorrect type
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\RestResponseException
     */
    public function testGetListWithIncorrectType()
    {
        $data = array(
            'type' => 'foo',
            'value' => 'bar'
        );

        $service = new CompaniesHouseService();

        $service->getList($data);
    }

    /**
     * Test getList throws exception
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\RestResponseException
     */
    public function testGetListThrowsException()
    {
        $data = array(
            'type' => 'nameSearch',
            'value' => 'Bobs bits n bobs ltd'
        );

        $service = $this->setUpService(array('getNewGateway', 'getServiceLocator', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->will($this->throwException(new \Exception('foo bar')));

        $service->getList($data);
    }

    /**
     * Test getList with name search with Error Response
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\RestResponseException
     */
    public function testGetListWithNameSearchErrorResponse()
    {
        $data = array(
            'type' => 'nameSearch',
            'value' => 'Bobs bits n bobs ltd'
        );

        $mockResponse = <<< XML
<?xml version="1.0"?>
<result>
    <GovTalkDetails>
        <GovTalkErrors>
            <Error>
                <Text>Error</Text>
                <Type>Error</Type>
                <Number>700</Number>
            </Error>
        </GovTalkErrors>
    </GovTalkDetails>
</result>
XML;

        $mockRequest = $this->getMock('\stdClass', array('setSearchRows'));

        $mockRequest->expects($this->once())
            ->method('setSearchRows');

        $mockGateway = $this->getMock('\stdClass', array('getNameSearch', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getNameSearch')
            ->with($data['value'])
            ->will($this->returnValue($mockRequest));

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($mockResponse));

        $service = $this->setUpService(array('getNewGateway', 'getServiceLocator', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->will($this->returnValue($mockGateway));

        $service->getList($data);
    }

    /**
     * Test getList with number search with Error Response
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\RestResponseException
     */
    public function testGetListWithNumberSearchErrorResponse()
    {
        $data = array(
            'type' => 'numberSearch',
            'value' => 12345
        );

        $mockResponse = <<< XML
<?xml version="1.0"?>
<result>
    <GovTalkDetails>
        <GovTalkErrors>
            <Error>
                <Text>Error</Text>
                <Type>Error</Type>
                <Number>700</Number>
            </Error>
        </GovTalkErrors>
    </GovTalkDetails>
</result>
XML;

        $mockRequest = $this->getMock('\stdClass', array('setSearchRows'));

        $mockRequest->expects($this->once())
            ->method('setSearchRows');

        $mockGateway = $this->getMock('\stdClass', array('getNumberSearch', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getNumberSearch')
            ->with($data['value'])
            ->will($this->returnValue($mockRequest));

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($mockResponse));

        $service = $this->setUpService(array('getNewGateway', 'getServiceLocator', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->will($this->returnValue($mockGateway));

        $service->getList($data);
    }

    /**
     * Test getList with companyDetails with Error Response
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\RestResponseException
     */
    public function testGetListWithCompanyDetailsErrorResponse()
    {
        $data = array(
            'type' => 'companyDetails',
            'value' => 12345
        );

        $mockResponse = <<< XML
<?xml version="1.0"?>
<result>
    <GovTalkDetails>
        <GovTalkErrors>
            <Error>
                <Text>Error</Text>
                <Type>Error</Type>
                <Number>700</Number>
            </Error>
        </GovTalkErrors>
    </GovTalkDetails>
</result>
XML;

        $mockRequest = $this->getMock('\stdClass');

        $mockGateway = $this->getMock('\stdClass', array('getCompanyDetails', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getCompanyDetails')
            ->with($data['value'])
            ->will($this->returnValue($mockRequest));

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($mockResponse));

        $service = $this->setUpService(array('getNewGateway', 'getServiceLocator', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->will($this->returnValue($mockGateway));

        $service->getList($data);
    }

    /**
     * Test getList with name search with Response Body
     */
    public function testGetListWithNameSearch()
    {
        $expected = array(
            'Count' => 3,
            'Results' => array(
                'Foo', 'Bar', 'Cake'
            )
        );

        $data = array(
            'type' => 'nameSearch',
            'value' => 'Bobs bits n bobs ltd'
        );

        $mockResponse = <<< XML
<?xml version="1.0"?>
<result>
    <Body>
        <NameSearch>
            <CoSearchItem>Foo</CoSearchItem>
            <CoSearchItem>Bar</CoSearchItem>
            <CoSearchItem>Cake</CoSearchItem>
        </NameSearch>
    </Body>
</result>
XML;

        $mockRequest = $this->getMock('\stdClass', array('setSearchRows'));

        $mockRequest->expects($this->once())
            ->method('setSearchRows');

        $mockGateway = $this->getMock('\stdClass', array('getNameSearch', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getNameSearch')
            ->with($data['value'])
            ->will($this->returnValue($mockRequest));

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($mockResponse));

        $service = $this->setUpService(array('getNewGateway', 'getServiceLocator', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->will($this->returnValue($mockGateway));

        $response = $service->getList($data);

        $this->assertEquals($expected, $response);
    }

    /**
     * Test getList with number search with Response Body
     */
    public function testGetListWithNumberSearch()
    {
        $expected = array(
            'Count' => 3,
            'Results' => array(
                'Foo', 'Bar', 'Cake'
            )
        );

        $data = array(
            'type' => 'numberSearch',
            'value' => 123456
        );

        $mockResponse = <<< XML
<?xml version="1.0"?>
<result>
    <Body>
        <NumberSearch>
            <CoSearchItem>Foo</CoSearchItem>
            <CoSearchItem>Bar</CoSearchItem>
            <CoSearchItem>Cake</CoSearchItem>
        </NumberSearch>
    </Body>
</result>
XML;

        $mockRequest = $this->getMock('\stdClass', array('setSearchRows'));

        $mockRequest->expects($this->once())
            ->method('setSearchRows');

        $mockGateway = $this->getMock('\stdClass', array('getNumberSearch', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getNumberSearch')
            ->with($data['value'])
            ->will($this->returnValue($mockRequest));

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($mockResponse));

        $service = $this->setUpService(array('getNewGateway', 'getServiceLocator', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->will($this->returnValue($mockGateway));

        $response = $service->getList($data);

        $this->assertEquals($expected, $response);
    }

    /**
     * Test getList with company details with Response Body
     */
    public function testGetListWithCompanyDetails()
    {
        $expected = array(
            'Count' => 3,
            'Results' => array(
                'Foo', 'Bar', 'Cake'
            )
        );

        $data = array(
            'type' => 'companyDetails',
            'value' => 12345678
        );

        $mockResponse = <<< XML
<?xml version="1.0"?>
<result>
    <Body>
        <CompanyDetails>Foo</CompanyDetails>
        <CompanyDetails>Bar</CompanyDetails>
        <CompanyDetails>Cake</CompanyDetails>
    </Body>
</result>
XML;

        $mockRequest = $this->getMock('\stdClass');

        $mockGateway = $this->getMock('\stdClass', array('getCompanyDetails', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getCompanyDetails')
            ->with($data['value'])
            ->will($this->returnValue($mockRequest));

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($mockResponse));

        $service = $this->setUpService(array('getNewGateway', 'getServiceLocator', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->will($this->returnValue($mockGateway));

        $response = $service->getList($data);

        $this->assertEquals($expected, $response);
    }

    /**
     * Test getList with unexpected response body
     */
    public function testGetListWithUnexpectedResponseBody()
    {
        $expected = array('Count' => 0, 'Results' => array());

        $data = array(
            'type' => 'companyDetails',
            'value' => 12345678
        );

        $mockResponse = <<< XML
<?xml version="1.0"?>
<result>
    <Foo></Foo>
</result>
XML;

        $mockRequest = $this->getMock('\stdClass');

        $mockGateway = $this->getMock('\stdClass', array('getCompanyDetails', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getCompanyDetails')
            ->with($data['value'])
            ->will($this->returnValue($mockRequest));

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($mockResponse));

        $service = $this->setUpService(array('getNewGateway', 'getServiceLocator', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->will($this->returnValue($mockGateway));

        $response = $service->getList($data);

        $this->assertEquals($expected, $response);
    }

    /**
     * Test getList with Current Company Officers with Response Body
     */
    public function testGetListWithCurrentCompanyOfficers()
    {
        $expected = array(
            'Count' => 1,
            'Results' => array(
                array(
                    'title'       => 'Title',
                    'forename'   => 'Forename',
                    'familyName'     => 'Surname',
                    'birthDate' => 'DOB'
                )
            )
        );

        $data = array(
            'type' => 'currentCompanyOfficers',
            'value' => 12345678
        );

        $mockResponse = <<< XML
<?xml version="1.0"?>
<result>
    <Body>
        <CompanyAppointments>
                <CoAppt>
                    <AppointmentType>DIR</AppointmentType>
                    <AppointmentStatus>CURRENT</AppointmentStatus>
                    <Person>
                        <Title>Title</Title>
                        <Forename>Forename</Forename>
                        <Surname>Surname</Surname>
                        <DOB>DOB</DOB>
                    </Person>
                </CoAppt>
        </CompanyAppointments>
    </Body>
</result>
XML;

        $mockRequest = $this->getMock('\stdClass');

        $mockGateway = $this->getMock('\stdClass', array('getCompanyAppointments', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getCompanyAppointments')
            ->with($data['value'])
            ->will($this->returnValue($mockRequest));

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($mockResponse));

        $service = $this->setUpService(array('getNewGateway', 'getServiceLocator', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->will($this->returnValue($mockGateway));

        $response = $service->getList($data);
        $this->assertEquals($expected, $response);
    }

    public function testCreateService()
    {
        $sut = m::mock('Dvsa\Olcs\Api\Service\CompaniesHouseService')->makePartial();

        $sm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $sm->shouldReceive('get')
            ->with('Config')
            ->andReturn(['config'])
            ->once();

        $companiesHouse = $sut->createService($sm);

        $this->assertEquals($sut, $companiesHouse);
    }
}
