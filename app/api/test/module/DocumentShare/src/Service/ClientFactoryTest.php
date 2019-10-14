<?php

namespace Dvsa\OlcsTest\DocumentShare\Service;

use Dvsa\Olcs\DocumentShare\Service\WebDavClient;
use Dvsa\Olcs\DocumentShare\Service\ClientFactory;

/**
 * Client Factory Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ClientFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideSetOptions
     * @param $config
     * @param $expected
     */
    public function testGetOptions($config, $expected)
    {
        $mockSl = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Configuration'))
            ->willReturn($config);

        $sut  = new ClientFactory();

        if ($expected instanceof \Exception) {
            $passed = false;
            try {
                $sut->getOptions($mockSl, 'testkey');
            } catch (\Exception $e) {
                if (
                    $e->getMessage() == $expected->getMessage() &&
                    get_class($e) == get_class($expected)
                ) {
                    $passed = true;
                }
            }

            $this->assertTrue($passed, 'Expected exception not thrown or message didn\'t match expected value');
        } else {
            $data = $sut->getOptions($mockSl, 'testkey');
            $this->assertEquals($expected, $data);
        }
    }

    public function provideSetOptions()
    {

        return array(
            array(array(), new \RuntimeException('Options could not be found in "document_share.testkey".')),
            array(
                array('document_share'=>array()),
                new \RuntimeException('Options could not be found in "document_share.testkey".')
            ),
            array(
                array('document_share'=>array('testkey'=>array('foo'=>'bar'))),
                array('foo'=>'bar')
            )
        );
    }

    public function testCanCreateServiceWithName()
    {
        $mockSl = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');

        $sut = new ClientFactory();
        $this->assertTrue($sut->canCreateServiceWithName($mockSl, '', WebDavClient::class));
        $this->assertFalse($sut->canCreateServiceWithName($mockSl, '', 'Data\\Service\\Backend\\Dummy'));
    }


    public function provideCreateService()
    {
        $configMissingBaseUri = array(
            'document_share' => array(
                'http' => array(),
                'client' => array(
                    'workspace' => 'test'
                )
            )
        );

        $configMisingWorkspace = array(
            'document_share' => array(
                'http' => array(),
                'client' => array(
                    'baseuri' => 'http://testdocument_share'
                )
            )
        );

        $config = array(
            'document_share' => array(
                'http' => array(),
                'client' => array(
                    'baseuri' => 'http://testdocument_share',
                    'workspace' => 'test'
                )
            )
        );

        $configWithUuid = array(
            'document_share' => array(
                'http' => array(),
                'client' => array(
                    'baseuri' => 'http://testdocument_share',
                    'workspace' => 'test',
                    'uuid' => 'u1234'
                )
            )
        );

        return array(
            array(
                $configMissingBaseUri,
                new \RuntimeException('Missing required option document_share.client.baseuri')
            ),
            array(
                $configMisingWorkspace,
                new \RuntimeException('Missing required option document_share.client.workspace')
            ),
            array(
                $config
            ),
            array(
                $configWithUuid
            )
        );
    }
}
