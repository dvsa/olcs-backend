<?php

namespace Dvsa\Olcs\Api\Domain\Query\BusRegSearchView;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;
use Dvsa\Olcs\Transfer\Query\OrderedQueryInterface;
use Dvsa\Olcs\Transfer\Query\OrderedTrait;
use Dvsa\Olcs\Transfer\Query\PagedQueryInterface;
use Dvsa\Olcs\Transfer\Query\PagedTrait;
use \Dvsa\Olcs\Transfer\Query\BusRegSearchView\BusRegSearchViewList as BusRegSearchViewTransfer;

/**
 * Class Bus Reg Search View List for Organisation or LA, optionally filtered by licence and bus reg status
 *
 * @Transfer\RouteName("backend/bus-reg-search-view-list")
 */
class BusRegSearchViewList extends BusRegSearchViewTransfer
{

    /**
     * @var int
     * @Transfer\Optional
     * @Transfer\Filter({"name":"Zend\Filter\Digits"})
     * @Transfer\Validator({"name":"Zend\Validator\Digits"})
     * @Transfer\Validator({"name":"Zend\Validator\GreaterThan", "options": {"min": 0}})
     */
    protected $organisationId;

    /**
     * @var int
     * @Transfer\Optional
     * @Transfer\Filter({"name":"Zend\Filter\Digits"})
     * @Transfer\Validator({"name":"Zend\Validator\Digits"})
     * @Transfer\Validator({"name":"Zend\Validator\GreaterThan", "options": {"min": 0}})
     */
    protected $localAuthorityId;

    /**
     * @return int
     */
    public function getOrganisationId()
    {
        return $this->organisationId;
    }

    /**
     * Setter required to apply default Organisation ID for Operator queries.
     *
     * @param int $organisationId
     */
    public function setOrganisationId($organisationId)
    {
        $this->organisationId = $organisationId;
    }

    /**
     * @return int
     */
    public function getLocalAuthorityId()
    {
        return $this->localAuthorityId;
    }

    /**
     * @param int $localAuthorityId
     */
    public function setLocalAuthorityId($localAuthorityId)
    {
        $this->localAuthorityId = $localAuthorityId;
    }
}
