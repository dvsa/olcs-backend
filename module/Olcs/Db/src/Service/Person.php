<?php

/**
 * Person Service
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Person Service
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class Person extends ServiceAbstract
{
    /**
     * Find licences from a given search
     *
     * @param array $options
     *
     * @return array
     */
    public function findPersons($options = array())
    {
        $optionConditions = array(
            'name' => array(
                'condition' => '(p.forename LIKE ? OR p.family_name LIKE ?)',
                'type' => 'LIKE'
            ),
            'forename' => array(
                'condition' => 'p.forename LIKE ?',
                'type' => 'LIKE'
            ),
            'familyName' => array(
                'condition' => 'p.family_name LIKE ?',
                'type' => 'LIKE'
            ),
            'birthDate' => array(
                'condition' => 'p.bith_date = ?',
                'type' => 'EQUALS'
            )
        );

        $lookupColumn = array(
            'name' => array('p.forename', 'p.family_name'),
            'forename' => 'p.forename',
            'familyName' => 'p.family_name',
            'birthDate' => 'p.birth_date',
        );

        $sortOrder = $this->getSortOrder($options);
        $orderByClause = $this->formatOrderByClause(
            $this->getOrderBy($options, $lookupColumn, $lookupColumn['name']), $sortOrder
        );

        list($conditions, $params) = $this->formatConditionsFromOptions($optionConditions, $options);

        $where = $this->formatWhereClause($conditions);

        $sql = 'SELECT p.* FROM person p ' . $where . $orderByClause;

        $dataQuery = $this->em->getConnection()->prepare($sql);
        $dataQuery->execute($params);
        $results = $dataQuery->fetchAll();

        return $results;
    }

    /**
     * Get the sort order from the options
     *
     * @param array $options
     * @param string $default
     * @return string
     */
    private function getSortOrder($options, $default = 'ASC')
    {
        return (
            isset($options['order']) && $options['order'] === 'DESC'
            ) ? 'DESC' : $default;
    }

    /**
     * Get the order by column name
     *
     * @param array $options
     * @param array $lookupColumn
     * @param string $default
     * @return string
     */
    private function getOrderBy($options, $lookupColumn, $default = '')
    {
        return (
            isset($options['sort']) && isset($lookupColumn[$options['sort']])
            ) ? $lookupColumn[$options['sort']] : $default;
    }

    /**
     * Format the order by statement
     *
     * @param string|array $orderBy
     * @param string $sortOrder
     *
     * @return string
     */
    private function formatOrderByClause($orderBy, $sortOrder)
    {
        if (is_array($orderBy)) {
            return 'ORDER BY ' . implode(' ' . $sortOrder . ', ', $orderBy) . ' ' . $sortOrder . ' ';
        }

        return 'ORDER BY ' . $orderBy . ' ' . $sortOrder . ' ';
    }

    /**
     * Format where clause
     *
     * @param array $conditions
     * @param bool $prependWhere
     * @return string
     */
    private function formatWhereClause($conditions, $prependWhere = true)
    {
        if (empty($conditions)) {
            return '';
        }

        return ($prependWhere ? 'WHERE ' : '') . '( ' . implode(') AND (', $conditions) . ') ';
    }

    /**
     * Format a value for binding
     *
     * @param string $value
     * @param string $type
     * @return string
     */
    private function formatValue($value, $type)
    {
        switch (strtoupper($type)) {
            case 'LIKE':
                return '%' . $value . '%';
            /**
             * These could be useful in the future
             */
            //case 'STARTS WITH':
            //    return $value . '%';
            //case 'ENDS WITH':
            //    return '%' . $value;
            case 'DATE':
                $parts = explode('-', $value);
                $newParts = array_map(
                    function ($value) {
                        return empty($value) ? '%' : $value;
                    }, $parts
                );
                return implode('-', $newParts);
            default:
                return $value;
        }
    }

    /**
     * Generate individual where clauses from the data passed in
     *
     * @param array $optionConditions
     * @param array $options
     * @return array
     */
    private function formatConditionsFromOptions($optionConditions, $options)
    {
        $conditions = $params = array();

        foreach ($optionConditions as $optionKey => $details) {

            if (isset($options[$optionKey])) {

                $value = $this->formatValue($options[$optionKey], $details['type']);

                $conditions[] = str_replace('?', ':' . $optionKey, $details['condition']);
                $params[':' . $optionKey] = $value;
            }
        }

        return array($conditions, $params);
    }
}
