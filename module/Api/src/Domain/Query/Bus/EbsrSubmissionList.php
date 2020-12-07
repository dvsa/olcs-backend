<?php

/**
 * EbsrSubmissionList
 */
namespace Dvsa\Olcs\Api\Domain\Query\Bus;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;
use Dvsa\Olcs\Transfer\Query\OrderedQueryInterface;
use Dvsa\Olcs\Transfer\Query\OrderedTrait;
use Dvsa\Olcs\Transfer\Query\PagedQueryInterface;
use Dvsa\Olcs\Transfer\Query\PagedTrait;

/**
 * EbsrSubmissionList
 */
final class EbsrSubmissionList extends AbstractQuery implements PagedQueryInterface, OrderedQueryInterface
{
    use PagedTrait;
    use OrderedTrait;

    /**
     * @Transfer\Filter({"name":"Laminas\Filter\Digits"})
     * @Transfer\Validator({"name":"Laminas\Validator\Digits"})
     * @Transfer\Validator({"name":"Laminas\Validator\GreaterThan", "options": {"min": 0}})
     * @Transfer\Optional
     */
    protected $organisation = null;

    /**
     * @var string
     * @Transfer\Optional
     * @Transfer\Filter({"name":"Laminas\Filter\StringTrim"})
     * @Transfer\Validator({
     *      "name":"Laminas\Validator\InArray",
     *      "options": {
     *          "haystack": {
     *              "ebsrt_new", "ebsrt_refresh"
     *          }
     *      }
     * })
     */
    protected $subType;

    /**
     * @var string
     * @Transfer\Optional
     * @Transfer\Filter({"name":"Laminas\Filter\StringTrim"})
     * @Transfer\Validator({"name":"Dvsa\Olcs\Transfer\Validators\EbsrSubmissionStatus"})
     */
    protected $status;

    /**
     * Gets subType
     *
     * @return string
     */
    public function getSubType()
    {
        return $this->subType;
    }

    /**
     * Gets status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Gets organisation
     *
     * @return int
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }
}
