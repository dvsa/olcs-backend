<?php

namespace Olcs\Db\Service\Search;

use Elastica\Aggregation\Terms;
use Elastica\Aggregation\Range;
use Elastica\Query;
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
        $elasticaQueryString  = new Query\Match();
        $elasticaQueryString->setField('_all', $query);

        $vrmQuery = new Query\Match();
        $vrmQuery->setField('vrm', $query);

        $postcodeQuery = new Query\Match();
        $postcodeQuery->setField('correspondence_postcode', $query);

        $wildcardQuery = strtolower(rtrim($query, '*') . '*');
        $elasticaQueryWildcard = new Query\Wildcard('org_name_wildcard', $wildcardQuery, 2.0);

        $elasticaQueryBool = new Query\Bool();
        $elasticaQueryBool->addShould($elasticaQueryWildcard);
        $elasticaQueryBool->addShould($vrmQuery);
        $elasticaQueryBool->addShould($postcodeQuery);
        $elasticaQueryBool->addShould($elasticaQueryString);

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
                $terms->setSize(30);
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
        $response['Results'] = [];

        $filter = new UnderscoreToCamelCase();

        foreach ($resultSet as $result) {
            /** @var \Elastica\Result $result */
            $raw = $result->getSource();
            $refined  = [];
            foreach ($raw as $key => $value) {
                $refined[lcfirst($filter->filter($key))] = $value;
            }

            $response['Results'][] = $refined;
        }

        $response['Filters'] = $this->processFilters($resultSet->getAggregations());

        return $response;
    }

    public function processFilters(array $aggregations)
    {
        $return = [];

        foreach ($aggregations as $aggregation => $value) {
            $return[$aggregation] = $value['buckets'];
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

            $this->filters[mb_strtolower($f->filter($filterName))] = $value;
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
