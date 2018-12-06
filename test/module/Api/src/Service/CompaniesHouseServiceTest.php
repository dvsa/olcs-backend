<?php

namespace OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\CompaniesHouseService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;

/**
 * @covers Dvsa\Olcs\Api\Service\CompaniesHouseService
 */
class CompaniesHouseServiceTest extends MockeryTestCase
{
    /** @var  m\MockInterface | \Zend\ServiceManager\ServiceLocatorInterface  */
    private $mockSl;

    public function setUp()
    {
        $cfg = [
            'companies_house_credentials' => [
                'userId' => 'unit_UserId',
                'password' => 'unit_Pass',
            ],
            'companies_house_connection' => [
                'proxy' => 'unit_Proxy',
            ],
        ];

        /** @var  \Zend\ServiceManager\ServiceLocatorInterface $mockSl */
        $this->mockSl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $this->mockSl->shouldReceive('get')->with('Config')->andReturn($cfg);
    }

    /**
     * Setup the service
     *
     * @param array $methods
     *
     * @return CompaniesHouseService | \PHPUnit_Framework_MockObject_MockObject $service
     */
    public function setUpService($methods = array())
    {
        /** @var CompaniesHouseService $sut */
        $sut = $this->createPartialMock(CompaniesHouseService::class, $methods);

        return $sut->createService($this->mockSl);
    }

    public function testGetNewGatewayProxyOn()
    {
        /** @var CompaniesHouseService $sut */
        $sut = new CompaniesHouseService();

        static::assertInstanceOf('CompaniesHouse\CHXmlGateway', $sut->getNewGateway());
    }

    public function testGetListWithoutType()
    {
        $expected = array('Count' => 0, 'Results' => array());

        $data = array(
            'value' => 'foo'
        );

        $sut = new CompaniesHouseService();

        static::assertEquals($expected, $sut->getList($data));
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

        static::assertEquals($expected, $service->getList($data));
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

    public function testGetListWithIncorrectType()
    {
        $data = array(
            'type' => 'foo',
            'value' => 'bar'
        );

        //  check
        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\RestResponseException::class);

        //  call
        $sut = (new CompaniesHouseService())->createService($this->mockSl);
        $sut->getList($data);
    }

    public function testGetListThrowsException()
    {
        $data = array(
            'type' => 'nameSearch',
            'value' => 'Bobs bits n bobs ltd'
        );

        //  check
        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\RestResponseException::class);

        $service = $this->setUpService(array('getNewGateway', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->will($this->throwException(new \Exception('foo bar')));

        $service->getList($data);
    }

    public function testGetListWithNameSearchErrorResponse()
    {
        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\RestResponseException::class);

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

        $mockRequest = $this->createPartialMock('\stdClass', array('setSearchRows'));

        $mockRequest->expects($this->once())
            ->method('setSearchRows');

        $mockGateway = $this->createPartialMock('\stdClass', array('getNameSearch', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getNameSearch')
            ->with($data['value'])
            ->willReturn($mockRequest);

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->willReturn($mockResponse);

        $service = $this->setUpService(array('getNewGateway', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->willReturn($mockGateway);

        $service->getList($data);
    }

    public function testGetListWithNumberSearchErrorResponse()
    {
        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\RestResponseException::class);

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

        $mockRequest = $this->createPartialMock('\stdClass', array('setSearchRows'));

        $mockRequest->expects($this->once())
            ->method('setSearchRows');

        $mockGateway = $this->createPartialMock('\stdClass', array('getNumberSearch', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getNumberSearch')
            ->with($data['value'])
            ->willReturn($mockRequest);

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->willReturn($mockResponse);

        $service = $this->setUpService(array('getNewGateway', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->willReturn($mockGateway);

        $service->getList($data);
    }

    public function testGetListWithCompanyDetailsErrorResponse()
    {
        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\RestResponseException::class);

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

        $mockRequest = $this->createMock('\stdClass');

        $mockGateway = $this->createPartialMock('\stdClass', array('getCompanyDetails', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getCompanyDetails')
            ->with($data['value'])
            ->willReturn($mockRequest);

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->willReturn($mockResponse);

        $service = $this->setUpService(array('getNewGateway', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->willReturn($mockGateway);

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

        $mockRequest = $this->createPartialMock('\stdClass', array('setSearchRows'));

        $mockRequest->expects($this->once())
            ->method('setSearchRows');

        $mockGateway = $this->createPartialMock('\stdClass', array('getNameSearch', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getNameSearch')
            ->with($data['value'])
            ->willReturn($mockRequest);

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->willReturn($mockResponse);

        $service = $this->setUpService(array('getNewGateway', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->willReturn($mockGateway);

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

        $mockRequest = $this->createPartialMock('\stdClass', array('setSearchRows'));

        $mockRequest->expects($this->once())
            ->method('setSearchRows');

        $mockGateway = $this->createPartialMock('\stdClass', array('getNumberSearch', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getNumberSearch')
            ->with($data['value'])
            ->willReturn($mockRequest);

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->willReturn($mockResponse);

        $service = $this->setUpService(array('getNewGateway', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->willReturn($mockGateway);

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

        $mockRequest = $this->createMock('\stdClass');

        $mockGateway = $this->createPartialMock('\stdClass', array('getCompanyDetails', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getCompanyDetails')
            ->with($data['value'])
            ->willReturn($mockRequest);

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->willReturn($mockResponse);

        $service = $this->setUpService(array('getNewGateway', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->willReturn($mockGateway);

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

        $mockRequest = $this->createMock('\stdClass');

        $mockGateway = $this->createPartialMock('\stdClass', array('getCompanyDetails', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getCompanyDetails')
            ->with($data['value'])
            ->willReturn($mockRequest);

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->willReturn($mockResponse);

        $service = $this->setUpService(array('getNewGateway', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->willReturn($mockGateway);

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

        $mockRequest = $this->createMock('\stdClass');

        $mockGateway = $this->createPartialMock('\stdClass', array('getCompanyAppointments', 'getResponse'));

        $mockGateway->expects($this->once())
            ->method('getCompanyAppointments')
            ->with($data['value'])
            ->willReturn($mockRequest);

        $mockGateway->expects($this->once())
            ->method('getResponse')
            ->willReturn($mockResponse);

        $service = $this->setUpService(array('getNewGateway', 'getService'));

        $service->expects($this->once())
            ->method('getNewGateway')
            ->willReturn($mockGateway);

        $response = $service->getList($data);
        $this->assertEquals($expected, $response);
    }

    public function testCreateService()
    {
        /** @var CompaniesHouseService $sut */
        $sut = m::mock(CompaniesHouseService::class)->makePartial();

        static::assertEquals($sut, $sut->createService($this->mockSl));
    }
}
