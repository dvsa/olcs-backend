<?php

/**
 * Trading Names REST controller
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */

namespace Olcs\Db\Controller;

use Zend\Http\Response;

/**
 * Trading Names REST controller
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class TradingNamesController extends AbstractBasicRestServerController
{
    protected $allowedMethods = array('create');

    /**
     * Create bunch of entities
     *
     * @param mixed $data
     * @return Response
     */
    public function create($data)
    {
        $this->checkMethod(__METHOD__);

        $data = $this->formatDataFromJson($data);

        if ($data instanceof Response) {

            return $data;
        }

        try {

            if (!empty($data['licence'])) {
                $this->getService('TradingName')->deleteList(array('licence' => $data['licence']));
            }
            if (!empty($data['organisation'])) {
                $this->getService('TradingName')->deleteList(array('organisation' => $data['organisation']));
            }

            foreach ($data['tradingNames'] as $tradingName) {
                $tradingName['licence'] = $data['licence'];
                $tradingName['organisation'] = $data['organisation'];
                $this->getService('TradingName')->create($tradingName);
            }

            return $this->respond(Response::STATUS_CODE_201, 'Entities Created', true);

        } catch (\Exception $ex) {

            return $this->unknownError($ex);
        }
    }
}
