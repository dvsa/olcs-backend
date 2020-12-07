<?php

namespace Olcs\Db\Service\Search;

use Elastica\Aggregation\Terms;
use Elastica\Query;
use Elastica\ResultSet;
use Olcs\Db\Exceptions\SearchDateFilterParseException;
use Laminas\Filter\Word\CamelCaseToUnderscore;
use Laminas\Filter\Word\UnderscoreToCamelCase;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Class Search
 *
 * @package Olcs\Db\Service\Search
 */
class Search implements AuthAwareInterface
{
    use AuthAwareTrait;

    const MAX_NUMBER_OF_RESULTS = 10000;

    /**
     * @var \Elastica\Client
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
     * Elastic client to use for making requests
     *
     * @param \Elastica\Client $client Client
     *
     * @return void
     */
    public function setClient(\Elastica\Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get the Elastic client
     *
     * @return \Elastica\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Get sort
     *
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set sort
     *
     * @param string $sort Sort
     *
     * @return void
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * Get order
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set order
     *
     * @param string $order Order
     *
     * @return void
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Submit a search request to elastic
     *
     * @param string $query   The string you are searching for
     * @param array  $indexes The indexes to search, this is now only used to idenitify which query template to use
     * @param int    $page    Starting page, for pagination
     * @param int    $limit   Number of results to return
     *
     * @return array
     */
    public function search($query, $indexes = [], $page = 1, $limit = 10)
    {
        $queryTemplate = $this->getQueryTemplate($indexes);
        if ($queryTemplate === false) {
            throw new \RuntimeException('Cannot generate an elasticsearch query, is the template missing');
        }
        $elasticaQuery = new QueryTemplate($queryTemplate, $query, $this->getFilters(), $this->getDateRanges());

        if (!empty($this->getSort()) && !empty($this->getOrder())) {
            $elasticaQuery->setSort([$this->getSort() => strtolower($this->getOrder())]);
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
                $terms->setOrder('_term', 'ASC');
                $terms->setSize(25);

                $elasticaQuery->addAggregation($terms);
            }
        }

        //Search on the index.
        $es = new \Elastica\Search($this->getClient());

        // Add indices, otherwise search is executed against all indices
        $es->addIndices($indexes);

        $response = [];
        $resultSet = $es->search($elasticaQuery);

        // Limit max number of results to prevent the ES "Result window is too large" error
        $response['Count'] = ($resultSet->getTotalHits() > self::MAX_NUMBER_OF_RESULTS)
            ? self::MAX_NUMBER_OF_RESULTS
            : $resultSet->getTotalHits();

        $response['Results'] = $this->processResults($resultSet);

        $response['Filters'] = $this->processFilters($resultSet->getAggregations());

        return $response;
    }

    /**
     * Get the query template if it exists
     *
     * @param array $indexes Indexes, used to identify which query template to use
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
     * Process results
     *
     * @param ResultSet $resultSet Result set
     *
     * @return array
     */
    protected function processResults(ResultSet $resultSet)
    {
        $f = new UnderscoreToCamelCase();

        $response = [];

        foreach ($resultSet as $result) {
            /** @var \Elastica\Result $result */
            $raw = $result->getSource();
            $refined = [];
            foreach ($raw as $key => $value) {
                $refined[lcfirst($f->filter($key))] = $value;
            }

            $response[] = $refined;
        }

        return $response;
    }

    /**
     * Process filters
     *
     * @param array $aggregations Aggregations
     *
     * @return array
     */
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
     * Get filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Sets the filters.
     *
     * @param array $filters Filters
     *
     * @return $this
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
     * Get date ranges
     *
     * @return array
     */
    public function getDateRanges()
    {
        return $this->dateRanges;
    }

    /**
     * Sets the filters.
     * Requires the dates in three parts ['year','month','day'] or [0-9]{4}\-[0-9]{2}\-[0-9]{2} string
     *
     * @param array $dateRanges Date ranges
     *
     * @return array
     */
    public function setDateRanges(array $dateRanges)
    {
        $f = new CamelCaseToUnderscore();

        foreach ($dateRanges as $filterName => $value) {
            if (is_array($value)) {
                $value = (!empty($value['year']) && !empty($value['month']) && !empty($value['day']))
                    ? sprintf('%04d-%02d-%02d', $value['year'], $value['month'], $value['day'])
                    : null;
                if (!is_null($value) && strtotime($value) === false) {
                    $exception = new SearchDateFilterParseException('invalid date filter');
                    $exception->setDateField($filterName);
                    throw $exception;
                }
            } elseif (is_string($value) && preg_match('/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/', $value)) {
                // value already matches the format required
            } else {
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
        $queryBool = new Query\BoolQuery();
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

        // Create a bulk request to update all the section 26 values
        $bulk = new \Elastica\Bulk($this->getClient());
        foreach ($resultSet->getResults() as $result) {
            /* @var $result \Elastica\Result */

            $action = new \Elastica\Bulk\Action(\Elastica\Bulk\Action::OP_TYPE_UPDATE);
            $action->setId($result->getId());
            $action->setType($result->getType());
            $action->setIndex($result->getIndex());
            $action->setSource(['doc' => ['section_26' => $section26Value ? 1 : 0]]);
            $bulk->addAction($action);
        }

        return $bulk->send()->isOk();
    }
}
