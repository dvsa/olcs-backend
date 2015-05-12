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
                    $elasticaQueryBool->addShould($elasticaQueryString);
                }
            }

            $vrmQuery = new Query\Match();
            $vrmQuery->setField('vrm', $query);
            $elasticaQueryBool->addShould($vrmQuery);

            $postcodeQuery = new Query\Match();
            $postcodeQuery->setField('correspondence_postcode', $query);
            $elasticaQueryBool->addShould($postcodeQuery);

            $wildcardQuery = strtolower(rtrim($query, '*') . '*');
            $elasticaQueryWildcard = new Query\Wildcard('org_name_wildcard', $wildcardQuery, 2.0);
            $elasticaQueryBool->addShould($elasticaQueryWildcard);

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
                $terms->setSize(5);
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
     * Sets the filters.
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
            } else {
                $value = null;
            }

            if (!empty($value)) {
                $this->dateRanges[strtolower($f->filter($filterName))] = $value;
            }
        }

        return $this;
    }
}
