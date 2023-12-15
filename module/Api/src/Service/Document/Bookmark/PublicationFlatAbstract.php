<?php

/**
 * PublicationFlatAbstract
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\PublicationBundle as Qry;

/**
 * PublicationFlatAbstract
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
abstract class PublicationFlatAbstract extends SingleValueAbstract
{
    const SRCH_VAL_KEY = 'publicationId'; // example
    const QUERY_CLASS = Qry::class;
}
