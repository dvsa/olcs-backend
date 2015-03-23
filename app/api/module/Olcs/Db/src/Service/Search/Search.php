<?php

namespace Olcs\Db\Service\Search;

use Elastica\Aggregation\Terms;
use Elastica\Query;
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
     * @param array $filters
     * @return array
     */
    public function search($query, $indexes = [], $page = 1, $limit = 10)
    {
        $elasticaQueryBool = new Query\Bool();

        $elasticaQueryString  = new Query\Match();
        $elasticaQueryString->setField('_all', $query);
        $elasticaQueryBool->addShould($elasticaQueryString);

        /**
         * Here we send the filter values selected to the search query
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

        $elasticaQuery        = new Query();
        $elasticaQuery->setQuery($elasticaQueryBool);
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
        $es    = new \Elastica\Search($this->getClient());

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
}
