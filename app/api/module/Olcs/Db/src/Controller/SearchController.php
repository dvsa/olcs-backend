<?php

namespace Olcs\Db\Controller;

use Olcs\Db\Exceptions\SearchDateFilterParseException;
use Zend\Http\PhpEnvironment\Response;

/**
 * Class SearchController
 *
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



        if (!empty($params['dateRanges']) && is_array($params['dateRanges'])) {
            try {
                $elastic->setDateRanges($params['dateRanges']);
            } catch (SearchDateFilterParseException $dateException) {
                return  $this->respond(
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
