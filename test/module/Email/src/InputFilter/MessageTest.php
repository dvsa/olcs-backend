<?php
namespace Olcs\Email\InputFilter;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Validator\ValidatorPluginManager;
use Zend\Filter\FilterPluginManager;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\ServiceManager\ServiceManager as ServiceLocator;
use Olcs\Email\Filter\StringToArray;

class MessageTest extends TestCase
{
    /**
     * @dataProvider messageDataProvider
     */
    public function testInputFilter($data, $valid)
    {
        $validatorManager = new ValidatorPluginManager();

        $filterManager = new FilterPluginManager();

        $inputFilterManager = new InputFilterPluginManager();

        $serviceLocator = new ServiceLocator();
        $serviceLocator->setService('ValidatorManager', $validatorManager);
        $serviceLocator->setService('FilterManager', $filterManager);
        $serviceLocator->setService('InputFilterManager', $inputFilterManager);

        $sut = new Message();
        /** @var \Zend\InputFilter\InputFilter $service */
        $service = $sut->createService($serviceLocator);

        $service->setData($data);

        $this->assertEquals($valid, $service->isValid($data));
    }

    public function messageDataProvider()
    {
        return [
            [
                [
                    'to'        => 'craig.reasbeck@valtech.co.uk',
                    'from'      => 'craig.reasbeck@valtech.co.uk',
                    'cc'        => 'craig.reasbeck@valtech.co.uk',
                    'bcc'       => 'craig.reasbeck@valtech.co.uk',
                    'subject'   => 'Email subject',
                    'message'   => 'Email message.',
                ],
                true
            ],
            [ // array emails
                [
                    'to'        => ['craig.reasbeck@valtech.co.uk'],
                    'from'      => 'craig.reasbeck@valtech.co.uk',
                    'cc'        => ['craig.reasbeck@valtech.co.uk'],
                    'bcc'       => ['craig.reasbeck@valtech.co.uk'],
                    'subject'   => 'Email subject',
                    'message'   => 'Email message.',
                ],
                true
            ],
            [ // empty message body
                [
                    'to'        => ['craig.reasbeck@valtech.co.uk'],
                    'from'      => 'craig.reasbeck@valtech.co.uk',
                    'cc'        => ['craig.reasbeck@valtech.co.uk'],
                    'bcc'       => ['craig.reasbeck@valtech.co.uk'],
                    'subject'   => 'Email subject',
                    'message'   => '',
                ],
                false
            ],
            [ // empty subject
                [
                    'to'        => ['craig.reasbeck@valtech.co.uk'],
                    'from'      => 'craig.reasbeck@valtech.co.uk',
                    'cc'        => ['craig.reasbeck@valtech.co.uk'],
                    'bcc'       => ['craig.reasbeck@valtech.co.uk'],
                    'subject'   => '',
                    'message'   => 'Email message.',
                ],
                false
            ],
            [ // empty cc + bcc
                [
                    'to'        => ['craig.reasbeck@valtech.co.uk'],
                    'from'      => 'craig.reasbeck@valtech.co.uk',
                    'cc'        => [],
                    'bcc'       => [],
                    'subject'   => 'Email subject',
                    'message'   => 'Email message.',
                ],
                true
            ],
            [ // list of emails
                [
                    'to'        => ['craig.reasbeck@valtech.co.uk', 'craig.reasbeck@valtech.co.uk'],
                    'from'      => 'craig.reasbeck@valtech.co.uk',
                    'cc'        => ['craig.reasbeck@valtech.co.uk'],
                    'bcc'       => ['craig.reasbeck@valtech.co.uk'],
                    'subject'   => 'Email subject',
                    'message'   => 'Email message.',
                ],
                true
            ],
            [ //no email
                [
                    'to'        => '',
                    'from'      => 'craig.reasbeck@valtech.co.uk',
                    'cc'        => '',
                    'bcc'       => '',
                    'subject'   => 'Email subject',
                    'message'   => 'Email message.',
                ],
                false
            ],
            [ //no subject
                [
                    'to'        => 'craig.reasbeck@valtech.co.uk',
                    'from'      => 'craig.reasbeck@valtech.co.uk',
                    'cc'        => '',
                    'bcc'       => '',
                    'subject'   => '',
                    'message'   => 'Email message.',
                ],
                false
            ],
            [ //no from
                [
                    'to'        => 'craig.reasbeck@valtech.co.uk',
                    'from'      => '',
                    'cc'        => '',
                    'bcc'       => '',
                    'subject'   => 'Email subject',
                    'message'   => 'Email message.',
                ],
                false
            ],
            [ //empty
                [],
                false
            ],
            [ // list of emails including one invalid
                [
                    'to'        => ['craig.reasbeck@valtech.co.uk', '@twitterUser'],
                    'from'      => 'craig.reasbeck@valtech.co.uk',
                    'cc'        => ['craig.reasbeck@valtech.co.uk'],
                    'bcc'       => ['craig.reasbeck@valtech.co.uk'],
                    'subject'   => 'Email subject',
                    'message'   => 'Email message.',
                ],
                false
            ],
        ];
    }
}
