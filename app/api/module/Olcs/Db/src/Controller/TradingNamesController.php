<?php

/**
 * Trading Names REST controller
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Olcs\Db\Controller;

use Zend\Http\Response;

/**
 * Trading Names REST controller
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
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
            $query = [
                'organisation' => $data['organisation']
            ];

            $existingRecords = $this->getService('TradingName')->getList($query);

            $existedNormalised = [];
            foreach ($existingRecords['Results'] as $record) {
                $existedNormalised[] = $record['name'];
            }
            $newNormalised = [];
            foreach ($data['tradingNames'] as $record) {
                $newNormalised[] = $record['name'];
            }
            $recordsToDelete = array_diff($existedNormalised, $newNormalised);
            $recordsToInsert = array_diff($newNormalised, $existedNormalised);
            foreach ($recordsToInsert as $record) {
                $dataToInsert = [
                    'licence' => $data['licence'],
                    'organisation' => $data['organisation'],
                    'name' => $record
                ];
                $this->getService('TradingName')->create($dataToInsert);
            }
            foreach ($recordsToDelete as $record) {
                $this->getService('TradingName')
                    ->deleteList(
                        [
                            'organisation' => $data['organisation'],
                            'name' => $record
                        ]
                    );
            }
            return $this->respond(Response::STATUS_CODE_201, 'Entities Created', true);

        } catch (\Exception $ex) {

            return $this->unknownError($ex);
        }
    }
}
