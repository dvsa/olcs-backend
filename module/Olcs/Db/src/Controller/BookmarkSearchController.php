<?php

/**
 * Bookmark Search REST controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace Olcs\Db\Controller;

use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;

/**
 * Bookmark Search REST controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class BookmarkSearchController extends AbstractBasicRestServerController
{
    protected $allowedMethods = array('getList');

    public function getList()
    {
        $queries = $this->getDataFromQuery();

        if (!isset($queries['bundle'])) {
            throw new RestResponseException('Please provide a bundle', Response::STATUS_CODE_500);
        }

        $queries = json_decode($queries['bundle'], true);

        // we expect query to be an associative array whose
        // keys are bookmark tokens and whose values are the queries
        // we need to run in order to fetch the data for said tokens

        $results = [];
        try {
            foreach ($queries as $token => $query) {
                $results[$token] = $this->getResultFromQuery($query);
            }
        } catch (\Exception $ex) {
            $this->getLogger()->info('Trapped exception querying bookmark: ' . $ex->getMessage());
            throw new RestResponseException($ex->getMessage(), Response::STATUS_CODE_500);
        }

        return $this->respond(Response::STATUS_CODE_200, 'OK', $results);
    }

    private function getResultFromQuery($query)
    {
        $query = $this->processQuery($query);

        // possibly a bit dangerous, but if the array is numeric we
        // assume it's a multi result request
        if (isset($query[0])) {
            $result = [];
            foreach ($query as $q) {
                $result[] = $this->getResult($q);
            }

            return $result;
        }

        return $this->getResult($query);
    }

    private function processQuery($query)
    {
        if (isset($query['options']['loop']) && $query['options']['loop']) {

            $newQueries = [];
            $queryTemplate = $query;
            unset($queryTemplate['options']['loop']);
            unset($queryTemplate['data']);

            foreach ($query['data'] as $data) {
                $newQuery = $queryTemplate;
                $newQuery['data'] = $data;
                $newQueries[] = $newQuery;
            }

            return $newQueries;
        }

        return $query;
    }

    private function getResult($query)
    {
        $service = $this->getService($query['service']);
        $data  = array_merge(
            $query['data'],
            [
                'bundle' => json_encode($query['bundle'])
            ]
        );

        if (isset($data['id'])) {
            return $service->get($data['id'], $data);
        }

        return $service->getList($data);
    }
}
