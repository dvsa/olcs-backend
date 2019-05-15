<?php

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

    public function getClassToTestName()
    {
        return $this->entityClass;
    }

    public function tearDown()
    {
        unset($this->entity);
        \Mockery::close();
    }

    protected function instantiate($entityName)
    {
        if (!method_exists($entityName, '__construct')) {
            $object = new $entityName();
        } else {
            $instantiator = new Instantiator();

            $object = $instantiator->instantiate($entityName);

            if (method_exists($object, 'initCollections')) {
                $object->initCollections();
            }
        }

        return $object;
    }

    public function testClearProperties()
    {
        $classToTestName = $this->getClassToTestName();
        $entity = $this->instantiate($classToTestName);

        $methodName = $this->getGettersAndSetters()[0][0];

        $properties = ['duntExist', lcfirst($methodName)];

        // Test clear properties (Non collection)
        $entity->{'set' . $methodName}('foo');
        $this->assertEquals('foo', $entity->{'get' . $methodName}());
        $entity->clearProperties($properties);
        $this->assertEquals(null, $entity->{'get' . $methodName}());

        // Test clear properties (Collection)
        $collection = new ArrayCollection(['foo', 'bar']);
        $entity->{'set' . $methodName}($collection);
        $this->assertSame($collection, $entity->{'get' . $methodName}());
        $entity->clearProperties($properties);
        $this->assertNotSame($collection, $entity->{'get' . $methodName}());
    }

    public function testLifecycleCallbacks()
    {
        $classToTestName = $this->getClassToTestName();
        $entity = $this->instantiate($classToTestName);

        $tested = false;

        if (method_exists($entity, 'setCreatedOnBeforePersist')) {
            $tested = true;
            $entity->setCreatedOnBeforePersist();
            $this->assertEquals(date('Y-m-d'), $entity->getCreatedOn()->format('Y-m-d'));
        }

        if (method_exists($entity, 'setLastModifiedOnBeforeUpdate')) {
            $tested = true;
            $entity->setLastModifiedOnBeforeUpdate();
            $this->assertEquals(date('Y-m-d'), $entity->getLastModifiedOn()->format('Y-m-d'));
        }

        if ($tested === false) {
            $this->assertTrue(true); // Mark the test as passed (None exist)
        }
    }

    /**
     * @dataProvider dataProviderAsDateTime
     */
    public function testGetDates($expected, $dateTime)
    {
        $hasAssert = false;

        $classToTestName = $this->getClassToTestName();

        $dateProperties = ['createdOn', 'lastModifiedOn', 'deletedDate'];
        foreach ($dateProperties as $property) {
            if (property_exists($classToTestName, $property)) {
                $entity = $this->instantiate($classToTestName);
                $setMethod = 'set'. $property;
                $getMethod = 'get'. $property;
                $entity->$setMethod($dateTime);
                $this->assertEquals($expected, $entity->$getMethod(true));
                $hasAssert = true;
            }
        }

        if (!$hasAssert) {
            $this->markTestSkipped(vsprintf('Does not have any of the following properties: %s', $dateProperties));
        }
    }

    public function dataProviderAsDateTime()
    {
        return [
            [new \DateTime('2017-09-29'), '2017-09-29'],
            [new \DateTime('2017-09-29'), new \DateTime('2017-09-29')],
            [null, null],
        ];
    }

    public function testGettersAndSetters()
    {
        foreach ($this->getGettersAndSetters() as $testCase) {
            list($methodName, $testValue) = $testCase;

            $classToTestName = $this->getClassToTestName();
            $entity = $this->instantiate($classToTestName);

            $entity->{'set' . $methodName}($testValue);
            $this->assertSame($testValue, $entity->{'get' . $methodName}());
        }
    }

    public function testAddMethods()
    {
        foreach ($this->getAddMethods() as $methodName) {
            if ($methodName == null) {
                $this->assertTrue(true); // Just mark the test as passed as there are no methods to test
                continue;
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
    }

    /**
     * @return array
     * @TODO abstract special cases, provide api to ignore certain fields
     */
    public function getGettersAndSetters()
    {
        $classToTestName = $this->getClassToTestName();

        $parts = explode('\\', $classToTestName);

        $class = array_pop($parts);
        $class = 'Abstract' . $class;

        $classToTestName = implode('\\', $parts) . '\\' . $class;

        $reflection = new \ReflectionClass($classToTestName);

        $methods = $reflection->getMethods();
        $testMethods = [];
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

                    $testMethods[] = array($methodName, $testValue);
                }
            }
        }
        return $testMethods;
    }

    /**
     * @return array
     */
    public function getAddMethods()
    {
        $classToTestName = $this->getClassToTestName();

        $parts = explode('\\', $classToTestName);

        $class = array_pop($parts);
        $class = 'Abstract' . $class;

        $classToTestName = implode('\\', $parts) . '\\' . $class;

        $reflection = new \ReflectionClass($classToTestName);

        $methods = $reflection->getMethods();

        $testMethods = array(null);

        foreach ($methods as $method) {
            if (substr($method->getName(), 0, 3) == 'add') {
                $testMethods[] = substr($method->getName(), 3);
            }
        }
        return $testMethods;
    }
}
