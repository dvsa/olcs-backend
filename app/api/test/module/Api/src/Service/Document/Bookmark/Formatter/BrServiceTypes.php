<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark\Formatter;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Formatter\BrServiceTypes;

/**
 * BrServiceTypes Test
 */
class BrServiceTypesTest extends AbstractArrayList
{
    const SUT_CLASS_NAME = BrServiceTypes::class;
    const ARRAY_FIELD = 'description';
    const EXCPECTED_OUTPUT = '3, abc, 2';
}
