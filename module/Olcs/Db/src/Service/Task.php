<?php

namespace Olcs\Db\Service;

class Task extends ServiceAbstract
{
    protected $validSearchFields = array(
        'owner', 'category'
    );

    public function getList($data)
    {
        $searchFields = $this->pickValidKeys($data, $this->getValidSearchFields());
        // @NOTE obviously this needs to be un-stubbed!
        $tasks = array(
            array(
                'id' => 1234,
                'type' => 'Application',
                'licNo' => 'OB12345678',
                'category' => 'Application',
                'subCategory' => 'Address change assisted digital',
                'description' => 'Address change',
                'date' => '2014-04-05 09:00:00',
                'owner' => 'Gillian Fox',
                'name' => 'Don Tarmacadam'
            ),
            array(
                'id' => 5678,
                'type' => 'Application',
                'licNo' => 'OB9876',
                'category' => 'Application',
                'subCategory' => 'A sub category',
                'description' => 'A task',
                'date' => '2014-06-03 09:00:00',
                'owner' => 'An Owner',
                'name' => 'A Name'
            )
        );

        if (count($searchFields)) {
            array_pop($tasks);
        }

        return array(
            'Count' => count($tasks),
            'Results' => $tasks
        );
    }
}
