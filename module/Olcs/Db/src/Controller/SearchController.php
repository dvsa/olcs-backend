<?php

namespace Olcs\Db\Controller;

use Olcs\Db\Exceptions\SearchDateFilterParseException;
use Laminas\Http\PhpEnvironment\Response;
use Olcs\Db\Service\Search\Search;

/**
 * Class SearchController
 *
 * @package Olcs\Db\Controller
 */
class SearchController extends AbstractController
{
    private Search $elasticSearchService;

    /**
     * @param Search $elasticSearchService
     */
    public function __construct(
        Search $elasticSearchService
    ) {
        $this->elasticSearchService = $elasticSearchService;
    }
    /**
     * Get list from search
     *
     * @return \Laminas\Http\Response $response http response
     */
    public function getList()
    {
        $params = array_merge((array)$this->params()->fromRoute(), (array)$this->params()->fromQuery());

        $indices = explode('|', $params['index']);

        if (isset($params['filters']) && !empty($params['filters']) && is_array($params['filters'])) {
            $this->elasticSearchService->setFilters($params['filters']);
        }
        if (!empty($params['dateRanges']) && is_array($params['dateRanges'])) {
            try {
                $this->elasticSearchService->setDateRanges($params['dateRanges']);
            } catch (SearchDateFilterParseException $dateException) {
                return $this->respond(
                    Response::STATUS_CODE_500,
                    'invalid date filter criteria',
                    ['error' => $dateException->getDateField()]
                );
            }
        }

        if (!empty($params['sort'])) {
            $this->elasticSearchService->setSort($params['sort']);
        }

        if (!empty($params['order'])) {
            $this->elasticSearchService->setOrder($params['order']);
        }

        $resultSet = $this->elasticSearchService->search(
            $params['q'],
            $indices,
            $params['page'],
            $params['limit']
        );

        return $this->respond(Response::STATUS_CODE_200, 'Results found', $resultSet);
    }
}
