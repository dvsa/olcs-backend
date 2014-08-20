<?php

/**
 * Abstract entity tester
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Db\Entity\Abstracts;

/**
 * Abstract entity tester
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class EntityTester extends \PHPUnit_Framework_TestCase
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

    /**
     * @dataProvider providerGettersAndSetters
     */
    public function testGettersAndSetters($methodName, $testValue)
    {
        $classToTestName = $this->getClassToTestName();
        $entity = new $classToTestName();

        $entity->{'set' . $methodName}($testValue);
        $this->assertSame($testValue, $entity->{'get' . $methodName}());
    }

    /**
     * @return array
     * @TODO abstract special cases, provide api to ignore certain fields
     */
    public function providerGettersAndSetters()
    {
        $classToTestName = $this->getClassToTestName();
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
}
