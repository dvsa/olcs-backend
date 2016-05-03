<?php

namespace Olcs\Db\Service\Search;

use Elastica\Aggregation\Terms;
use Elastica\Query;
use Elastica\Filter;
use Elastica\ResultSet;
use Zend\Filter\Word\CamelCaseToUnderscore;
use Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * Class Search
 * @package Olcs\Db\Service\Search
 */
class Search
{
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
     * @param $query
     * @param array $indexes
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function search($query, $indexes = [], $page = 1, $limit = 10)
    {
        $elasticaQueryBool = new Query\Bool();

        /*
         * Check for a single asterisk to allow the query to run with no params.
         * Just returns everything for instances where landing on a search page
         */
        if ($query == '*' ) {
            // ignore all query params and just search index for everything
            $elasticaQuery        = new Query();
        } else {
            $elasticaQueryString  = new Query\Match();
            $elasticaQueryString->setField('_all', $query);
            $elasticaQueryBool->addShould($elasticaQueryString);

            $elasticaQueryBool = $this->processDateRanges($elasticaQueryBool);

            /**
             * Here we send the filters.
             */
            $filters = $this->getFilters();
            foreach ($filters as $field => $value) {

                if (!empty($value)) {

                    $elasticaQueryString = new Query\Match();
                    $elasticaQueryString->setField($field, $value);
                    $elasticaQueryBool->addMust($elasticaQueryString);
                }
            }

            foreach ($indexes as $index) {
                $this->modifyQueryForIndex($index, $query, $elasticaQueryBool);
            }

            $elasticaQuery = new Query();

            $elasticaQuery->setQuery($elasticaQueryBool);

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

        foreach ($indexes as $index) {
            $es->addIndex($index);
        }

        $response = [];

        $resultSet = $es->search($elasticaQuery);

        $response['Count'] = $resultSet->getTotalHits();

        $response['Results'] = $this->processResults($resultSet);

        $response['Filters'] = $this->processFilters($resultSet->getAggregations());

        return $response;
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

                $wildcardQuery = strtolower(rtrim($search, '*') . '*');
                $elasticaQueryWildcard = new Query\Wildcard('org_name_wildcard', $wildcardQuery, 2.0);
                $queryBool->addShould($elasticaQueryWildcard);

                break;
            case 'application':
            case 'case':
                $correspondencePostcodeQuery = new Query\Match();
                $correspondencePostcodeQuery->setField('correspondence_postcode', $search);
                $queryBool->addShould($correspondencePostcodeQuery);

                $wildcardQuery = strtolower(rtrim($search, '*') . '*');
                $elasticaQueryWildcard = new Query\Wildcard('org_name_wildcard', $wildcardQuery, 2.0);
                $queryBool->addShould($elasticaQueryWildcard);
                break;
            case 'operator':
                $postcodeQuery = new Query\Match();
                $postcodeQuery->setField('postcode', $search);
                $queryBool->addShould($postcodeQuery);

                $wildcardQuery = strtolower(rtrim($search, '*') . '*');
                $elasticaQueryWildcard = new Query\Wildcard('org_name_wildcard', $wildcardQuery, 2.0);
                $queryBool->addShould($elasticaQueryWildcard);

                break;
            case 'irfo':
            case 'licence':
            case 'psv_disc':
            case 'publication':
            case 'user':
                $wildcardQuery = strtolower(rtrim($search, '*') . '*');
                $elasticaQueryWildcard = new Query\Wildcard('org_name_wildcard', $wildcardQuery, 2.0);
                $queryBool->addShould($elasticaQueryWildcard);

                break;
            case 'vehicle_current':
            case 'vehicle_removed':
                $vrmQuery = new Query\Match();
                $vrmQuery->setField('vrm', $search);
                $queryBool->addShould($vrmQuery);

                $wildcardQuery = strtolower(rtrim($search, '*') . '*');
                $elasticaQueryWildcard = new Query\Wildcard('org_name_wildcard', $wildcardQuery, 2.0);
                $queryBool->addShould($elasticaQueryWildcard);

                break;
            case 'person':
                $wildcardQuery = strtolower(rtrim($search, '*') . '*');
                $elasticaQueryWildcard = new Query\Wildcard('org_name_wildcard', $wildcardQuery, 2.0);
                $queryBool->addShould($elasticaQueryWildcard);

                // apply search term to forename and family name wildcards
                $wildcardQuery = '*'. strtolower(trim($search, '*')). '*';
                $queryBool->addShould(
                    new Query\Wildcard('person_family_name_wildcard', $wildcardQuery, 2.0)
                );
                $queryBool->addShould(
                    new Query\Wildcard('person_forename_wildcard', $wildcardQuery, 2.0)
                );

                $parts = explode(' ', $search);
                if (count($parts) > 1) {
                    // apply wildcard to each search term
                    foreach ($parts as $part_search) {
                        $wildcardQuery = '*' . strtolower(trim($part_search, '*')) . '*';
                        $queryBool->addShould(
                            new Query\Wildcard('person_family_name_wildcard', $wildcardQuery, 2.0)
                        );
                        $queryBool->addShould(
                            new Query\Wildcard('person_forename_wildcard', $wildcardQuery, 2.0)
                        );
                    }
                }

                break;
            case 'busreg':
                $queryMatch = new Query\Match();
                $queryMatch->setFieldQuery('reg_no', $search);
                $queryMatch->setFieldBoost('reg_no', 2);
                $queryBool->addShould($queryMatch);

                $wildcardQuery = strtolower(rtrim($search, '*') . '*');
                $elasticaQueryWildcard = new Query\Wildcard('org_name_wildcard', $wildcardQuery, 2.0);
                $queryBool->addShould($elasticaQueryWildcard);

                break;
        }
    }

    public function processDateRanges(Query\Bool $bool)
    {
        /**
         * Here we send the filter values selected to the search query
         */
        $dates = $this->getDateRanges();

        foreach ($dates as $fieldName => $value) {

            if (strtolower(substr($fieldName, -2)) == 'to') {
                // we'll deal with the TO fields later.
                continue;
            }

            $criteria = [];

            $range = new Query\Range();

            if (strtolower(substr($fieldName, -4)) == 'from') {
                $fieldName = substr($fieldName, 0, -5);
                $criteria['from'] = $value;

                // Let's now look for the to field.
                $toFieldName = $fieldName . '_to';
                if (array_key_exists($toFieldName, $dates)) {
                    if ('' != $dates[$toFieldName]) {
                        $criteria['to'] = $dates[$toFieldName];
                    }
                }
            }

            $range->addField($fieldName, $criteria);
            $bool->addMust($range);
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
