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
        $params = array_merge($this->params()->fromRoute(), $this->params()->fromQuery());

        $indices = explode('|', $params['index']);

        $elastic = $this->getServiceLocator()->get('ElasticSearch\Search');

        $params['filters'] = [
            'orgTypeDesc' => '',
            'orgName' => '',
            'licenceTrafficArea' => ''
        ];

        if (isset($params['filters']) && !empty($params['filters']) && is_array($params['filters'])) {

            $elastic->setFilters($params['filters']);
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
