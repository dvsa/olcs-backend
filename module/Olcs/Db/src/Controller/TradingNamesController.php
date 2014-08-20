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

            $this->getService('TradingName')->deleteAll(array('licence' => $data['licence']));
            $this->getService('TradingName')->deleteAll(array('organisation' => $data['organisation']));

            $response = array();

            foreach ($data['tradingNames'] as $tradingName) {
                $tradingName['licence'] = $data['licence'];
                $tradingName['organisation'] = $data['organisation'];
                $id = $this->getService('TradingName')->create($tradingName);
                $response[] = array('id' => $id);
            }

            return $this->respond(Response::STATUS_CODE_201, 'Entities Created', $response);

        } catch (\Exception $ex) {

            return $this->unknownError($ex);
        }
    }
}
