<?php

/**
 * Person Service
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Operator Service
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class Operator extends ServiceAbstract
{

    /**
     * Returns an indexed array of valid search terms for this service / entity.
     *
     * @return array
     */
    public function getValidSearchFields()
    {
        return array('name');
    }

    /**
     * Find operators from a given search
     *
     * @param array $options
     *
     * @return array
     */
    public function findAllByLicence($options = array())
    {
        $optionConditions = array(
            'name' => array(
                'condition' => '(o.name LIKE ?)',
                'type' => 'LIKE'
            )
        );

        $lookupColumn = array(
            'name' => 'o.name',
        );

        $sortOrder = $this->getSortOrder($options);
        $orderByClause = $this->formatOrderByClause(
            $this->getOrderBy($options, $lookupColumn, $lookupColumn['name']), $sortOrder
        );

        list($conditions, $params) = $this->formatConditionsFromOptions($optionConditions, $options);

        $where = $this->formatWhereClause($conditions);

        $sql = 'SELECT o.* FROM organisation o ' . $where . $orderByClause;

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
     * Get the limit
     *
     * @param array $options
     * @param int $default
     * @return int
     */
    private function getLimit($options, $default = 10)
    {
        return (
            isset($options['limit']) && is_numeric($options['limit']) && $options['limit'] > 0
            ) ? (int) $options['limit'] : $default;
    }

    /**
     * Get the page number
     *
     * @param array $options
     * @param int $default
     * @return int
     */
    private function getPageNumber($options, $default = 1)
    {
        return (
            isset($options['page']) && is_numeric($options['page']) && $options['page'] > 0
            ) ? (int) $options['page'] : $default;
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
     * Format limit clause
     *
     * @param int $limit
     * @param int $offset
     */
    private function formatLimitClause($limit, $offset = null)
    {
        if (empty($offset)) {
            return 'LIMIT ' . $limit;
        } else {
            return 'LIMIT ' . $offset . ',' . $limit;
        }
    }

    /**
     * Format where clause
     *
     * @param bool $prependWhere
     * @param array $conditions
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
