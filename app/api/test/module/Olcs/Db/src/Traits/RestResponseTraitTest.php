<?php

/**
 * Tests RestResponseTrait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Db\Service;

use Laminas\Http\Response;

/**
 * Tests RestResponseTrait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RestResponseTraitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test that getNewResponse returns a new instance
     *
     * @group Traits
     * @group RestResponseTrait
     */
    public function testGetNewResponse()
    {
        $trait = $this->getMockForTrait('\Olcs\Db\Traits\RestResponseTrait');

        $response = $trait->getNewResponse();

        $this->assertTrue($response instanceof Response);

        $response2 = $trait->getNewResponse();

        $this->assertTrue($response2 instanceof Response);

        $this->assertFalse($response === $response2);
    }

    /**
     * Test respond
     *
     * @dataProvider providerRespond
     *
     * @group Traits
     * @group RestResponseTrait
     */
    public function testRespond($input, $expected)
    {
        $expectedContent = json_encode(
            array(
                'Response' => array(
                    'Code' => $expected['code'],
                    'Message' => $expected['reasonPhrase'],
                    'Summary' => $expected['summary'],
                    'Data' => $expected['data']
                )
            )
        );

        $mockResponse = $this->createPartialMock(
            Response::class,
            array(
                'setStatusCode',
                'setContent',
                'getReasonPhrase'
            )
        );

        $trait = $this->getMockForTrait(
            '\Olcs\Db\Traits\RestResponseTrait',
            array(),
            '',
            true,
            true,
            true,
            // This argument is an array of mocked methods
            array('getNewResponse')
        );

        $mockResponse->expects($this->once())
            ->method('setStatusCode')
            ->with($expected['code']);

        $mockResponse->expects($this->once())
            ->method('setContent')
            ->with($expectedContent);

        $mockResponse->expects($this->once())
            ->method('getReasonPhrase')
            ->will($this->returnValue($expected['reasonPhrase']));

        $trait->expects($this->once())
            ->method('getNewResponse')
            ->will($this->returnValue($mockResponse));

        switch (count($input)) {
            case 1:
                $response = $trait->respond($input[0]);
                break;
            case 2:
                $response = $trait->respond($input[0], $input[1]);
                break;
            case 3:
                $response = $trait->respond($input[0], $input[1], $input[2]);
                break;
        }

        $this->assertEquals($response, $mockResponse);
    }

    /**
     * Provider for respond
     *
     * @return array
     */
    public function providerRespond()
    {
        return array(
            array(
                array(Response::STATUS_CODE_200),
                array(
                    'code' => Response::STATUS_CODE_200,
                    'reasonPhrase' => 'Some Phrase',
                    'summary' => null,
                    'data' => array()
                )
            ),
            array(
                array(Response::STATUS_CODE_400, 'Summary'),
                array(
                    'code' => Response::STATUS_CODE_400,
                    'reasonPhrase' => 'Some Phrase',
                    'summary' => 'Summary',
                    'data' => array()
                )
            ),
            array(
                array(Response::STATUS_CODE_404, 'Summary', array('foo' => 'bar')),
                array(
                    'code' => Response::STATUS_CODE_404,
                    'reasonPhrase' => 'Some Phrase',
                    'summary' => 'Summary',
                    'data' => array('foo' => 'bar')
                )
            )
        );
    }
}
