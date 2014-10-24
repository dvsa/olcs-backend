<?php

namespace Olcs\Db\Controller;

use Zend\Http\PhpEnvironment\Response;

class SearchController extends AbstractController
{
    public function getList()
    {
        $params = array_merge($this->params()->fromRoute(), $this->params()->fromQuery());

        $elastic = $this->getServiceLocator()->get('ElasticSearch\Search');
        $resultSet = $elastic->search($params['query'], [$params['index']], $params['page'], $params['limit'] );

        return $this->respond(Response::STATUS_CODE_200, 'Results found', $resultSet);
    }
}
