<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark\Stub;

use Dvsa\Olcs\Api\Service\Document\Bookmark\AbstractPublicationLinkSection;

class AbstractPublicationLinkSectionStub extends AbstractPublicationLinkSection
{
    const TEST_PUB_TYPE_SECTION = 'unit_pubTypeSctn';

    const TEST_SECTION_ID = 7777;

    protected $pubTypeSection = [
        self::TEST_PUB_TYPE_SECTION => [
            self::TEST_SECTION_ID,
            self::PUB_SECTION_18,
        ],
    ];
}
