<?php

namespace Olcs\Db\Service\Search;

use Dvsa\Olcs\Api\Entity\Publication\Publication;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Elastica\Aggregation\Terms;
use Elastica\Query;
use Elastica\Filter;
use Elastica\ResultSet;
use Zend\Filter\Word\CamelCaseToUnderscore;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Class Search
 * @package Olcs\Db\Service\Search
 */
class Search implements AuthAwareInterface
{
    use AuthAwareTrait;

    /**
     * @var
     */
    protected $client;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var array
     */
    protected $dateRanges = [];

    /**
     * @var string
     */
    protected $sort = '';

    /**
     * @var string
     */
    protected $order = '';

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param string $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param string $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @param $query
     * @param array $indexes
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function search($query, $indexes = [], $page = 1, $limit = 10)
    {
        /** @var  $elasticaQueryBoolMain Query/Bool
         * Main query boolean that allows any filters to work as Logical ANDs with the main
         * search query string. */
        $elasticaQueryBoolMain = new Query\Bool();

        $elasticaQueryBool = new Query\Bool();

        // First check to see if the index should use the new query templates
        // @todo Once all searches are using the new query templates, a lot of this code can be removed
        $queryTemplate = $this->getQueryTemplate($indexes);
        if ($queryTemplate !== false) {
            $elasticaQuery = new QueryTemplate($queryTemplate, $query);
        } elseif ($query == '*' ) {
            /*
             * Check for a single asterisk to allow the query to run with no params.
             * Just returns everything for instances where landing on a search page
             */
            $elasticaQuery = new Query();
        } else {
            // Generate _all_search as logical OR
            $elasticaQueryString  = new Query\Match();
            $elasticaQueryString->setField('_all', $query);
            $elasticaQueryBool->addShould($elasticaQueryString);

            // add date ranges as logical AND
            $elasticaQueryBoolMain = $this->processDateRanges($elasticaQueryBoolMain);

            foreach ($indexes as $index) {
                // amend query depending on index
                $this->modifyQueryForIndex($index, $query, $elasticaQueryBool);
            }

            /**
             * Here we send the filters as logical AND.
             */
            $filters = $this->getFilters();
            foreach ($filters as $field => $value) {

                if (!empty($value)) {

                    $elasticaQueryString = new Query\Match();
                    $elasticaQueryString->setField($field, $value);

                    // Add filter as logical AND
                    $elasticaQueryBoolMain->addMust($elasticaQueryString);
                }
            }

            $elasticaQueryBoolMain->addMust($elasticaQueryBool);

            $elasticaQuery = new Query();

            $elasticaQuery->setQuery($elasticaQueryBoolMain);

            if (!empty($this->getSort()) && !empty($this->getOrder())) {
                $elasticaQuery->setSort([$this->getSort() => strtolower($this->getOrder())]);
            }
        }

        $elasticaQuery->setSize($limit);
        $elasticaQuery->setFrom($limit * ($page - 1));

        /**
         * This deals with asking elastic for the filters / aggregation terms we want.
         */
        $filterNames = $this->getFilterNames();
        if (isset($filterNames)) {
            foreach ($filterNames as $filterName) {

                $terms = new Terms($filterName);
                $terms->setField($filterName);
                $terms->setMinimumDocumentCount(1);

                $elasticaQuery->addAggregation($terms);
            }
        }

        //Search on the index.
        $es = new \Elastica\Search($this->getClient());

        // If not using QueryTemplate then need to add indexes
        if (!$elasticaQuery instanceof QueryTemplate) {
            foreach ($indexes as $index) {
                $es->addIndex($index);
            }
        }

        $response = [];

        $resultSet = $es->search($elasticaQuery);

        $response['Count'] = $resultSet->getTotalHits();

        $response['Results'] = $this->processResults($resultSet);

        $response['Filters'] = $this->processFilters($resultSet->getAggregations());

        return $response;
    }

    /**
     * Get the query template if it exists
     *
     * @param array $indexes Indexes
     *
     * @return string|bool Path and file of the template, or false if doesn't exist
     */
    private function getQueryTemplate($indexes)
    {
        if ($this->isAnonymousUser() || $this->isExternalUser()) {
            $file = __DIR__ . '/templates/selfserve/' . $indexes[0] . '.json';
        } else {
            $file = __DIR__ . '/templates/' . $indexes[0] . '.json';
        }

        if (file_exists($file)) {
            return $file;
        }

        return false;
    }

    /**
     * Modify the query dependant on the index
     *
     * @param string $index
     * @param string $search
     * @param \Elastica\Query\Bool $queryBool
     */
    private function modifyQueryForIndex($index, $search, Query\Bool $queryBool)
    {
        switch ($index) {
            case 'address':
                $postcodeQuery = new Query\Match();
                $postcodeQuery->setField('postcode', $search);
                $queryBool->addShould($postcodeQuery);
                break;
            case 'application':
                $correspondencePostcodeQuery = new Query\Match();
                $correspondencePostcodeQuery->setField('correspondence_postcode', $search);
                $queryBool->addShould($correspondencePostcodeQuery);

                if (is_numeric($search)) {
                    // searching for empty string causes exception
                    $applicationIdQuery = new Query\Match();
                    $applicationIdQuery->setField('app_id', $search);
                    $queryBool->addShould($applicationIdQuery);
                }
                $queryBool->addShould($this->generateOrgNameWildcardQuery($search));
                break;
            case 'case':
                $correspondencePostcodeQuery = new Query\Match();
                $correspondencePostcodeQuery->setField('correspondence_postcode', $search);
                $queryBool->addShould($correspondencePostcodeQuery);

                if (is_numeric($search)) {
                    // searching for empty string causes exception
                    $caseIdQuery = new Query\Match();
                    $caseIdQuery->setField('case_id', $search);
                    $queryBool->addShould($caseIdQuery);
                }
                $queryBool->addShould($this->generateOrgNameWildcardQuery($search));
                break;
            case 'operator':
                $postcodeQuery = new Query\Match();
                $postcodeQuery->setField('postcode', $search);
                $queryBool->addShould($postcodeQuery);
                $queryBool->addShould($this->generateOrgNameWildcardQuery($search));
                break;
            case 'publication':
                if ($this->isAnonymousUser() || !$this->isInternalUser()) {
                    $statusQuery = new Query\Match();
                    $statusQuery->setField('pub_status', Publication::PUB_PRINTED_STATUS);
                    $queryBool->addMust($statusQuery);
                }
                $queryBool->addShould($this->generateOrgNameWildcardQuery($search));
                break;
            case 'irfo':
                $queryBool->addShould($this->generateOrgNameWildcardQuery($search));
                break;
            case 'psv_disc':
                $queryBool->addShould($this->generateOrgNameWildcardQuery($search));
                break;
            case 'licence':
                $queryBool->addShould($this->generateOrgNameWildcardQuery($search));
                break;
            case 'user':
                $queryBool->addShould($this->generateOrgNameWildcardQuery($search));

                // OLCS-12130
                if ($this->isInternalUser()) {
                    $loginMatch = new Query\Match();
                    $loginMatch->setFieldQuery('login_id', $search);
                    $loginMatch->setFieldBoost('login_id', 2.0);
                    $queryBool->addShould($loginMatch);

                    $licNosMatch = new Query\Match();
                    $licNosMatch->setFieldQuery('lic_nos', $search);
                    $queryBool->addShould($licNosMatch);
                }

                break;
            case 'vehicle_current':
            case 'vehicle_removed':
                $vrmQuery = new Query\Match();
                $vrmQuery->setField('vrm', $search);
                $queryBool->addShould($vrmQuery);

                $queryBool->addShould($this->generateOrgNameWildcardQuery($search));

                break;
            case 'person':
                if (is_numeric($search)) {
                    // OLCS-12934 look up by single ID
                    $queryBool->addShould($this->addQueryMatch('person_id', $search, 2.0));
                    $queryBool->addShould($this->addQueryMatch('tm_id', $search, 2.0));
                } else {
                    // apply search term to forename and family name wildcards
                    $wildcardQuery = '*' . strtolower(trim($search, '*')) . '*';
                    $queryBool->addShould(
                        new Query\Wildcard('person_family_name_wildcard', $wildcardQuery, 2.0)
                    );
                    $queryBool->addShould(
                        new Query\Wildcard('person_forename_wildcard', $wildcardQuery, 1.0)
                    );
                }

                /*
                 * Hide Removed TMs from SS and Anonymous users
                 *
                 * The permission check below first checks for anonymous users. This is because isInternalUser()
                 * method doesnt handle anon users (yet).
                 *
                 * Use of Filtered Query will be deprecated in the future.
                 * @see https://www.elastic.co/blog/better-query-execution-coming-elasticsearch-2-0
                 */
                if ($this->isAnonymousUser() || !$this->isInternalUser()) {
                    $statusQuery = new Query\Match();
                    $statusQuery->setField('tm_status_id', TransportManager::TRANSPORT_MANAGER_STATUS_REMOVED);
                    $queryBool->addMustNot($statusQuery);

                    // Add must have licence no
                    $licenceQuery = new Query\Filtered();
                    $licenceFilter = new Filter\Exists('lic_id');
                    $licenceQuery->setFilter($licenceFilter);
                    $queryBool->addMust($licenceQuery);
                }

                // separate search into words
                $search = preg_replace('/\s{2,}/', ' ', $search);
                $parts = explode(' ', $search);

                if (count($parts) > 1) {
                    // apply wildcard to each search term
                    foreach ($parts as $part_search) {
                        // only search if valid
                        if (!empty($part_search)) {
                            $wildcardQuery = '*' . strtolower(trim($part_search, '*')) . '*';
                            $queryBool->addShould(
                                new Query\Wildcard('person_family_name_wildcard', $wildcardQuery, 2.0)
                            );
                            $queryBool->addShould(
                                new Query\Wildcard('person_forename_wildcard', $wildcardQuery, 1.0)
                            );
                        }
                    }
                }

                break;
            case 'busreg':
                $queryMatch = new Query\Match();
                $queryMatch->setFieldQuery('reg_no', $search);
                $queryMatch->setFieldBoost('reg_no', 2);
                $queryBool->addShould($queryMatch);

                $queryBool->addShould($this->generateOrgNameWildcardQuery($search));

                break;
        }
    }

    /**
     * Generates and returns the wildcard query for Org Name
     *
     * @param $search
     * @return Query\Wildcard
     */
    private function generateOrgNameWildcardQuery($search)
    {
        $wildcardQuery = strtolower(rtrim($search, '*') . '*');
        $elasticaQueryWildcard = new Query\Wildcard('org_name_wildcard', $wildcardQuery, 2.0);

        return $elasticaQueryWildcard;
    }

    /**
     * Wrapper function to generate a simple field match
     *
     * @param $field
     * @param $search
     * @return Query\Match
     */
    private function addQueryMatch($field, $search, $boost = null)
    {
        $queryMatch = new Query\Match();
        $queryMatch->setFieldQuery($field, $search);
        $queryMatch->setFieldBoost($field, $boost);
        return $queryMatch;
    }

    /**
     * Process the date ranges against the query.
     *
     * @param Query\Bool $bool
     * @return Query\Bool
     */
    public function processDateRanges(Query\Bool $bool)
    {
        /**
         * Here we send the filter values selected to the search query
         */
        $dates = $this->getDateRanges();

        foreach ($dates as $fieldName => $value) {
            $lcFieldName = strtolower($fieldName);

            if (substr($lcFieldName, -11) === 'from_and_to') {
                /* from_and_to allows a single date field to be used as a terms filter whilst keeping the
                 * individual Day/Month/Year input fields. The 'from_and_to' is identified and a single date is
                 * added as a query match rather than a range (for efficiency)
                 */
                $fieldName = substr($fieldName, 0, -12);

                $queryMatch = new Query\Match();
                $queryMatch->setFieldQuery($fieldName, $value);
                $bool->addMust($queryMatch);

            } elseif (substr($lcFieldName, -4) === 'from') {
                $criteria = [];

                $fieldName = substr($fieldName, 0, -5);
                $criteria['from'] = $value;

                // Let's now look for the to field.
                $toFieldName = $fieldName . '_to';
                if (!empty($dates[$toFieldName])) {
                    $criteria['to'] = $dates[$toFieldName];
                }

                $range = new Query\Range();
                $range->addField($fieldName, $criteria);
                $bool->addMust($range);
            }
        }

        return $bool;
    }

    protected function processResults(ResultSet $resultSet)
    {
        $f = new UnderscoreToCamelCase();

        $response = [];

        foreach ($resultSet as $result) {
            /** @var \Elastica\Result $result */
            $raw = $result->getSource();
            $refined  = [];
            foreach ($raw as $key => $value) {
                $refined[lcfirst($f->filter($key))] = $value;
            }

            $response[] = $refined;
        }

        return $response;
    }

    protected function processFilters(array $aggregations)
    {
        $return = [];

        $f = new UnderscoreToCamelCase();

        foreach ($aggregations as $aggregation => $value) {
            $return[lcfirst($f->filter($aggregation))] = $value['buckets'];
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Sets the filters.
     *
     * @param array $filters
     *
     * @return array
     */
    public function setFilters(array $filters)
    {
        $f = new CamelCaseToUnderscore();

        foreach ($filters as $filterName => $value) {

            $this->filters[strtolower($f->filter($filterName))] = $value;
        }

        return $this;
    }

    /**
     * Returns an array of filter names (array keys from the $this->requiredFilters array)
     *
     * @return array
     */
    public function getFilterNames()
    {
        return array_keys($this->getFilters());
    }

    /**
     * @return array
     */
    public function getDateRanges()
    {
        return $this->dateRanges;
    }

    /**
     * Sets the filters. Requires the dates in three parts ['y','m','d']
     *
     * @param array $filters
     *
     * @return array
     */
    public function setDateRanges(array $dateRanges)
    {
        $f = new CamelCaseToUnderscore();

        foreach ($dateRanges as $filterName => $value) {

            if (is_array($value) && !empty(trim(implode(" ", [$value['year'], $value['month'], $value['day']])))) {
                $value = implode('-', [$value['year'], $value['month'], $value['day']]);
            } elseif (false === preg_match('/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/', $value)) {
                $value = null;
            }

            if (!empty($value)) {
                $this->dateRanges[strtolower($f->filter($filterName))] = $value;
            }
        }

        return $this;
    }

    /**
     * Update the section 26 attribute in the vehicle indexes
     *
     * @param array $ids            Array of vehicle.id
     * @param bool  $section26Value Set or unset the value
     *
     * @return boolean If success
     */
    public function updateVehicleSection26(array $ids, $section26Value)
    {
        // Build a query to search where vehicle id is one of the IDs
        $queryBool = new Query\Bool();
        foreach ($ids as $id) {
            $match = new Query\Match();
            $match->setField('veh_id', $id);
            $queryBool->addShould($match);
        }

        $query = new Query();
        $query->setQuery($queryBool);
        // set size to a large value
        $query->setSize(1000);

        // Search both vehicle indexes
        $search = new \Elastica\Search($this->getClient());
        $search->addIndex('vehicle_current');
        $search->addIndex('vehicle_removed');
        $resultSet = $search->search($query);

        // No results found, therefore nothing to do
        if ($resultSet->count() === 0) {
            return true;
        }

        // Create a bulk request to upate all the section 26 values
        $bulk = new \Elastica\Bulk($this->getClient());
        foreach ($resultSet->getResults() as $result) {
            /* @var $result \Elastica\Result */

            $action = new \Elastica\Bulk\Action(\Elastica\Bulk\Action::OP_TYPE_UPDATE);
            $action->setId($result->getId());
            $action->setType($result->getType());
            $action->setIndex($result->getIndex());
            $action->setSource(['doc' => ['section_26'=> $section26Value ? 1 : 0]]);
            $bulk->addAction($action);
        }

        return $bulk->send()->isOk();
    }
}
