<?php

/**
 * Abstract entity tester
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Entity\Abstracts;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Instantiator\Instantiator;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Abstract entity tester
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class EntityTester extends MockeryTestCase
{
    /**
     * Holds the entity
     *
     * @var object
     */
    protected $entity;

    /**
     * Holds the entity class name
     *
     * @var string
     */
    protected $entityClass;

    /**
     * @var array
     */
    protected $testMethods = [];

    public function getClassToTestName()
    {
        return $this->entityClass;
    }

    protected function instantiate($entityName)
    {
        $instantiator = new Instantiator();

        $object = $instantiator->instantiate($entityName);

        if (method_exists($object, 'initCollections')) {
            $object->initCollections();
        }

        return $object;
    }

    /**
     * @dataProvider providerGettersAndSetters
     */
    public function testGettersAndSetters($methodName, $testValue)
    {
        $classToTestName = $this->getClassToTestName();
        $entity = $this->instantiate($classToTestName);

        $entity->{'set' . $methodName}($testValue);
        $this->assertSame($testValue, $entity->{'get' . $methodName}());
    }

    /**
     * @dataProvider providerAddMethods
     */
    public function testAddMethods($methodName)
    {
        if ($methodName == null) {
            $this->assertTrue(true); // Just mark the test as passed as there are no methods to test
            return;
        }

        $classToTestName = $this->getClassToTestName();
        $entity = $this->instantiate($classToTestName);

        $this->assertEquals(0, count($entity->{'get' . $methodName}()));

        $entity->{'add' . $methodName}('foo');
        $entity->{'add' . $methodName}('bar');
        $entity->{'add' . $methodName}('cake');

        $this->assertEquals(3, count($entity->{'get' . $methodName}()));

        $entity->{'remove' . $methodName}('bar');

        $this->assertEquals(2, count($entity->{'get' . $methodName}()));

        $collection = new ArrayCollection(array('bish', 'bash', 'bosh'));

        $entity->{'add' . $methodName}($collection);

        $this->assertEquals(5, count($entity->{'get' . $methodName}()));
    }

    /**
     * @return array
     * @TODO abstract special cases, provide api to ignore certain fields
     */
    public function providerGettersAndSetters()
    {
        $classToTestName = $this->getClassToTestName();

        $parts = explode('\\', $classToTestName);

        $class = array_pop($parts);
        $class = 'Abstract' . $class;

        $classToTestName = implode('\\', $parts) . '\\' . $class;

        $reflection = new \ReflectionClass($classToTestName);

        $methods = $reflection->getMethods();
        foreach ($methods as $method) {
            if (substr($method->getName(), 0, 3) == 'set') {
                $methodName = substr($method->getName(), 3);

                if ((ltrim($method->getDeclaringClass()->getName(), "\\") == ltrim($classToTestName, "\\")) &&
                    $method->isPublic() &&
                    $reflection->hasProperty(lcfirst($methodName)) &&
                    $reflection->hasMethod('get' . $methodName)
                ) {
                    // If this $parameter->getClass() is not null, one of the methods is type-hinted.
                    foreach ($method->getParameters() as $parameter) {
                        if ($parameter->getClass() !== null) {
                            continue 2;
                        }
                    }

                    if ($methodName == 'Id') {
                        $testValue = rand(10000, 200000);
                    } elseif ($methodName == 'IsDeleted') {
                        $testValue = 1;
                    } else {
                        $testValue = $methodName . '_test_' . rand(10000, 200000);
                    }

                    $this->testMethods[] = array($methodName, $testValue);
                }
            }
        }
        return $this->testMethods;
    }

    /**
     * @return array
     */
    public function providerAddMethods()
    {
        $classToTestName = $this->getClassToTestName();

        $parts = explode('\\', $classToTestName);

        $class = array_pop($parts);
        $class = 'Abstract' . $class;

        $classToTestName = implode('\\', $parts) . '\\' . $class;

        $reflection = new \ReflectionClass($classToTestName);

        $methods = $reflection->getMethods();

        $testMethods = array(
            array(null)
        );

        foreach ($methods as $method) {
            if (substr($method->getName(), 0, 3) == 'add') {
                $methodName = substr($method->getName(), 3);

                $testMethods[] = array($methodName);
            }
        }
        return $testMethods;
    }
}
