<?php

namespace Olcs\Db\Service\Search;

use Elastica\Query;
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

        return $response;
    }
}
