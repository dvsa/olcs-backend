<?php

/**
 * Tests SubmissionDecisionRecommendation Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Service\SubmissionDecisionRecommendation;

/**
 * Tests SubmissionDecisionRecommendation Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SubmissionDecisionRecommendationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the service
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->service = new SubmissionDecisionRecommendation();
    }

    /**
     * Test getValidSearchFields
     */
    public function testGetValidSearchFields()
    {
        $expected = array();

        $this->assertEquals($expected, $this->service->getValidSearchFields());
    }
}
