<?php

namespace Olcs\Db\Service\Search;

use Elastica\Query\QueryString;
use Elastica\Query;
use Zend\Filter\Word\UnderscoreToCamelCase;

class Search
{
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

    public function search($query, $indexes = [], $page = 1, $limit = 10)
    {
        $elasticaQueryString  = new QueryString($query);

        $elasticaQuery        = new Query();
        $elasticaQuery->setQuery($elasticaQueryString);
        $elasticaQuery->setSize($limit);
        $elasticaQuery->setFrom($limit*($page -1));

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