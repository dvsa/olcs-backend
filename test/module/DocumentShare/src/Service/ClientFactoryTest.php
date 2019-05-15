<?php

namespace Dvsa\OlcsTest\DocumentShare\Service;

use Dvsa\Olcs\DocumentShare\Service\ClientFactory;

/**
 * Client Factory Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ClientFactoryTest extends \PHPUnit\Framework\TestCase
{
    private const TEST_BASEURI = "testdocument_share";

    /**
     * @dataProvider provideCreateService
     * @param $config
     * @param $expected
     */
    public function testCreateService($config, $expected = null)
    {
        $mockSl = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Configuration'))
            ->willReturn($config);

        $sut = new ClientFactory();
        if ($expected instanceof \Exception) {
            $passed = false;
            try {
                $sut->createService($mockSl);
            } catch (\Exception $e) {
                if ($e->getMessage() == $expected->getMessage() && get_class($e) == get_class($expected)) {
                    $passed = true;
                }
            }

            $this->assertTrue($passed, 'Expected exception not thrown or message didn\'t match expected value');
        } else {
            $sut->createService($mockSl);
        }
    }

    public function provideCreateService()
    {
        $configMissingBaseUri = array(
            'document_share' => array(
                'http' => array(),
                'client' => array(
                    'workspace' => 'test',
                    'username' => 'test',
                    'password' => 'test'
                )
            )
        );

        $configMisingWorkspace = array(
            'document_share' => array(
                'http' => array(),
                'client' => array(
                    'baseuri' => self::TEST_BASEURI,
                    'username' => 'test',
                    'password' => 'test'
                )
            )
        );

        $config = array(
            'document_share' => array(
                'http' => array(),
                'client' => array(
                    'baseuri' => self::TEST_BASEURI,
                    'workspace' => 'test',
                    'username' => 'test',
                    'password' => 'test'
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
            )
        );
    }
}
