<?php

namespace Dvsa\Olcs\Api\Domain\Query\Document;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;
use Dvsa\Olcs\Transfer\Util\Annotation as Transfer;

/**
 * Class ByDocumentStoreId
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class ByDocumentStoreId extends AbstractQuery
{
    /**
     * @Transfer\String
     * @Transfer\Filter({"name":"Laminas\Filter\StringTrim"})
     * @Transfer\Validator({"name":"Laminas\Validator\StringLength", "options":{"max":1000}})
     */
    protected $documentStoreId = '';

    public function getDocumentStoreId(): string
    {
        return $this->documentStoreId;
    }
}
