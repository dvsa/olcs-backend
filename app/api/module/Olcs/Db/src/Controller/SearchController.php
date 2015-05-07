<?php

namespace Olcs\Db\Controller;

use Zend\Http\PhpEnvironment\Response;

/**
 * Class SearchController
 * @package Olcs\Db\Controller
 */
class SearchController extends AbstractController
{
    public function getList()
    {
        $params = array_merge((array)$this->params()->fromRoute(), (array)$this->params()->fromQuery());

        $indices = explode('|', $params['index']);

        /** @var \Olcs\Db\Service\Search\Search $elastic */
        $elastic = $this->getServiceLocator()->get('ElasticSearch\Search');

        if (isset($params['filters']) && !empty($params['filters']) && is_array($params['filters'])) {

            $elastic->setFilters($params['filters']);
        }

        if (isset($params['dateRanges']) && !empty($params['dateRanges']) && is_array($params['dateRanges'])) {

            $elastic->setDateRanges($params['dateRanges']);
        }

        $resultSet = $elastic->search(
            urldecode($params['query']),
            $indices,
            $params['page'],
            $params['limit']
        );

        return $this->respond(Response::STATUS_CODE_200, 'Results found', $resultSet);
    }
}
