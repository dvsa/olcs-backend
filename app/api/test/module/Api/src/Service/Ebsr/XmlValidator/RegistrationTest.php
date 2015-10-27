<?php


namespace Dvsa\OlcsTest\Api\Service\Ebsr\XmlValidator;

use Dvsa\Olcs\Api\Service\Ebsr\XmlValidator\Registration;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class RegistrationTest
 * @package OlcsTest\Ebsr\src\Validator\Structure
 */
class RegistrationTest extends TestCase
{
    /**
     * @param $xml
     * @param $valid
     * @dataProvider isValidProvider
     */
    public function testIsValid($xml, $valid)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($xml);

        $sut = new Registration();

        $this->assertEquals($valid, $sut->isValid($dom));
    }

    public function isValidProvider()
    {
        return [
            ['<Registrations></Registrations>', false],
            [
                '<Registrations><Registration></Registration><Registration></Registration></Registrations>',
                false
            ],
            ['<Registrations><Registration></Registration></Registrations>', true]
        ];
    }
}
