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

    /**
     * Any extra date properties to be tested.
     *
     * @var array
     */
    protected $extraDateProperties = [];

    public function getClassToTestName()
    {
        return $this->entityClass;
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

    public function testGetDates()
    {
        if (empty($this->extraDateProperties)) {
            $this->assertTrue(true); //nothing to test
            return;
        }

        $date = '2017-09-29';
        $dateTime = new \DateTime($date);

        foreach ($this->extraDateProperties as $property) {
            $entity = $this->instantiate($this->entityClass);
            $setMethod = 'set' . $property;
            $getMethod = 'get' . $property;

            //add as a date string
            $entity->$setMethod($date);
            $this->assertEquals($dateTime, $entity->$getMethod(true));

            //add as a datetime
            $entity->$setMethod($dateTime);
            $this->assertEquals($dateTime, $entity->$getMethod(true));

            //test when date is null
            $entity->$setMethod(null);
            $this->assertNull($entity->$getMethod());
        }
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

            $collection = new ArrayCollection(['bish', 'bash', 'bosh']);

            $entity->{'add' . $methodName}($collection);

            $this->assertEquals(5, count($entity->{'get' . $methodName}()));
        }
    }

    /**
     * @return array
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

                if (
                    (ltrim($method->getDeclaringClass()->getName(), "\\") == ltrim($classToTestName, "\\")) &&
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

                    $testMethods[] = [$methodName, $testValue];
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

        $testMethods = [null];

        foreach ($methods as $method) {
            if (substr($method->getName(), 0, 3) == 'add') {
                $testMethods[] = substr($method->getName(), 3);
            }
        }
        return $testMethods;
    }
}
