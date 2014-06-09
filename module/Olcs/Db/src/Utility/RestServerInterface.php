<?php

/**
 * RestServerInterface
 *
 * @author Someone <someone@somewhere.co.uk>
 */
namespace Olcs\Db\Utility;

/**
 * RestServerInterface
 *
 * @author Someone <someone@somewhere.co.uk>
 */
interface RestServerInterface
{
    /**
     * Should enter a value into the database and return the
     * identifier for the record that has been created.
     *
     * @param array $data
     * @return mixed
     */
    public function create($data);

    /**
     * Returns a list of matching records.
     *
     * @return array
     */
    public function getList();

    /**
     * Gets a matching record by identifying value.
     *
     * @param string|int $id
     *
     * @return array
     */
    public function get($id);

    /**
     * Updates the entire record based on identifying value.
     *
     * @param mixed $id
     * @param array $data
     *
     * @return boolean success or failure
     */
    public function update($id, $data);

    /**
     * Updates the partial record based on identifying value.
     *
     * @param mixed $id
     * @param array $data
     *
     * @return boolean success or failure
     */
    public function patch($id, $data);

    /**
     * Deletes record based on identifying value.
     *
     * @param mixed $id
     *
     * @return boolean success or failure
     */
    public function delete($id);
}
