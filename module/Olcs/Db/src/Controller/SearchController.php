<?php

namespace Olcs\Db\Controller;

use Olcs\Db\Exceptions\SearchDateFilterParseException;
use Laminas\Http\PhpEnvironment\Response;

/**
 * Class SearchController
 *
 * @package Olcs\Db\Controller
 */
class SearchController extends AbstractController
{
    /**
     * Get list from search
     *
     * @return \Laminas\Http\Response $response http response
     */
    public function getList()
    {
        $params = array_merge((array)$this->params()->fromRoute(), (array)$this->params()->fromQuery());

        $indices = explode('|', $params['index']);

        /** @var \Olcs\Db\Service\Search\Search $elastic */
        $elastic = $this->getServiceLocator()->get('ElasticSearch\Search');
        if (isset($params['filters']) && !empty($params['filters']) && is_array($params['filters'])) {
            $elastic->setFilters($params['filters']);
        }
        if (!empty($params['dateRanges']) && is_array($params['dateRanges'])) {
            try {
                $elastic->setDateRanges($params['dateRanges']);
            } catch (SearchDateFilterParseException $dateException) {
                return $this->respond(
                    Response::STATUS_CODE_500,
                    'invalid date filter criteria',
                    ['error' => $dateException->getDateField()]
                );
            }
        }

        if (!empty($params['sort'])) {
            $elastic->setSort($params['sort']);
        }

        if (!empty($params['order'])) {
            $elastic->setOrder($params['order']);
        }

        $resultSet = $elastic->search(
            $params['q'],
            $indices,
            $params['page'],
            $params['limit']
        );

        return $this->respond(Response::STATUS_CODE_200, 'Results found', $resultSet);
    }
}
