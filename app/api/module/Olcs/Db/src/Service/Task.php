<?php

namespace Olcs\Db\Service;

class Task extends ServiceAbstract
{
    public function getList($data)
    {
        // @NOTE obviously this needs to be un-stubbed!
        $tasks = array(
            array(
                'id' => 1234,
                'type' => 'Application',
                'licenceNumber' => 'OB12345678',
                'category' => 'Application',
                'subCategory' => 'Address change assisted digital',
                'description' => 'Address change',
                'date' => '2014-04-5 09:00:00',
                'owner' => 'Gillian Fox',
                'name' => 'Don Tarmacadam'
            )
        );

        return array(
            'Count' => count($tasks),
            'Results' => $tasks
        );
    }
}
