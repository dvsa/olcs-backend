<?php

/**
 * Licence Service
 *  - Takes care of the CRUD actions Licence entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Licence Service
 *  - Takes care of the CRUD actions Licence entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Licence extends ServiceAbstract
{

    /**
     * Returns an indexed array of valid search terms for this service / entity.
     *
     * @return array
     */
    public function getValidSearchFields()
    {
        return array();
    }

    /**
     * Find licences from a given search
     *
     * @param array $options
     *
     * @return array
     */
    public function findLicences($options = array())
    {
        $licenceSearchAddressParts = array(
            'a.address_line1',
            'a.address_line2',
            'a.address_line3',
            'a.address_line4',
            'a.town',
            'a.postcode'
        );

        $optionConditions = array(
            'operatorName' => array(
                'condition' => 'o.name LIKE ?',
                'type' => 'LIKE'
            ),
            'entityType' => array(
                'condition' => 'o.organisation_type = ?',
                'type' => 'EQUALS'
            ),
            'licNo' => array(
                'condition' => 'l.lic_no LIKE ?',
                'type' => 'LIKE'
            ),
            'postcode' => array(
                'condition' => 'a.postcode LIKE ?',
                'type' => 'LIKE'
            ),
            'address' => array(
                'condition' => implode(' LIKE ? OR ', $licenceSearchAddressParts) . ' LIKE ?',
                'type' => 'LIKE'
            ),
            'town' => array(
                'condition' => 'a.town LIKE ? OR l.addressTown LIKE ?',
                'type' => 'LIKE'
            ),
            'operatorId' => array(
                'condition' => 'o.id LIKE ?',
                'type' => 'LIKE'
            )
        );

        $lookupColumn = array(
            'licNo' => 'l.lic_no',
            'appId' => 'app.id',
            'operatorName' => 'o.name',
            'companyNumber' => 'o.registered_company_number',
            'lastActionDate' => 'l.startDate',
            'correspondenceAddress' => $licenceSearchAddressParts,
            'operatorCentre' => 'l.addressLine1',
            'caseNumber' => 'caseCount',
            'mlh' => 'l.id'
        );

        $sortOrder = $this->getSortOrder($options);
        $page = $this->getPageNumber($options);
        $limit = $this->getLimit($options);
        $offset = $this->getOffset($page, $limit);
        $limitClause = $this->formatLimitClause($limit, $offset);
        $orderByClause = $this->formatOrderByClause(
            $this->getOrderBy($options, $lookupColumn, $lookupColumn['licNo']), $sortOrder
        );

        list($conditions, $params) = $this->formatConditionsFromOptions($optionConditions, $options);

        $where = $this->formatWhereClause($conditions);

        $dataSql = 'SELECT l.*, o.*, a.*, app.id as appNumber, app.status as appStatus,
            l.lic_no AS licNo, l.id AS licenceId, count(c.id) AS caseCount ';

        $countSql = 'SELECT COUNT(DISTINCT l.id) AS resultCount ';

        // Common part of the query
        $sql = 'FROM organisation o
INNER JOIN licence l ON l.organisation_id = o.id
LEFT JOIN application app ON app.licence_uid = l.id
LEFT JOIN contact_details cd ON (cd.organisation_id = o.id AND cd.contact_details_type = \'correspondence\')
LEFT JOIN address a ON cd.address_id = a.id
LEFT JOIN trading_name tm ON l.id = tm.F_Licence_UID
LEFT OUTER JOIN cases c ON c.licence=l.id
' . $where;

        $dataSql .= $sql . ' GROUP BY l.id ' . $orderByClause . $limitClause;

        $countSql .= $sql;

        $countQuery = $this->em->getConnection()->prepare($countSql);
        $countQuery->execute($params);
        $countResults = $countQuery->fetchAll();

        $dataQuery = $this->em->getConnection()->prepare($dataSql);
        $dataQuery->execute($params);
        $results = $dataQuery->fetchAll();

        return array($countResults[0], $results);
    }

    /**
     * Find persons from a given search
     *
     * @param array $options
     *
     * @return array
     */
    public function findAllPersons($options)
    {
        $optionConditions = array(
            'forename' => array(
                'condition' => 'p.forename LIKE ?',
                'type' => 'LIKE'
            ),
            'familyName' => array(
                'condition' => 'p.familyName LIKE ?',
                'type' => 'LIKE'
            ),
            'birthDate' => array(
                'condition' => 'p.date_of_birth = ?',
                'type' => 'EQUALS'
            )
        );

        $lookupColumn = array(
            'name' => array('p.forename', 'p.familyName'),
            'forename' => 'p.forename',
            'familyName' => 'p.familyName',
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
     * Find persons and licences from a given search
     *
     * @param array $options
     *
     * @return array
     */
    public function findPersonsAndLicences($options)
    {
        $optionConditions = array(
            'forename' => array(
                'condition' => 'p.forename LIKE ?',
                'type' => 'LIKE'
            ),
            'familyName' => array(
                'condition' => 'p.familyName = ?',
                'type' => 'LIKE'
            ),
            'birthDate' => array(
                'condition' => 'p.birth_date LIKE ?',
                'type' => 'DATE'
            )
        );

        $lookupColumn = array(
            'licence' => 'l.lic_no',
            'disqualification' => 'disqualificationStatus',
        );

        $sortOrder = $this->getSortOrder($options);
        $page = $this->getPageNumber($options);
        $limit = $this->getLimit($options);
        $offset = $this->getOffset($page, $limit);
        $limitClause = $this->formatLimitClause($limit, $offset);
        $orderByClause = $this->formatOrderByClause(
            $this->getOrderBy($options, $lookupColumn, $lookupColumn['licence']), $sortOrder
        );

        list($conditions, $params) = $this->formatConditionsFromOptions($optionConditions, $options);

        $where = $this->formatWhereClause($conditions);

        $dataSql = 'SELECT l.id AS licenceId, l.lic_no AS licNo, l.status AS licenceStatus,
MAX(IF(pd.status = \'Y\', 0, 1)) AS disqualificationStatus ';

        $countSql = 'SELECT COUNT(DISTINCT p.id, IFNULL(l.id, 0)) AS rowCount ';

        // Common part of the query
        $sql = 'FROM person p
LEFT JOIN person_disqualification pd ON p.id = pd.F_Person_UID
LEFT JOIN organisation_owner po ON p.id = po.F_Person_UID
LEFT JOIN organisation o ON po.F_Organisation_UID = o.id
LEFT JOIN tms_licence_link tll ON p.id = tll.person_id
LEFT JOIN licence l ON (l.id = tll.licence_id OR l.operatorId = o.id) ' . $where;

        $dataSql .= $sql . ' GROUP BY p.id, l.id ' . $orderByClause . $limitClause;

        $countSql .= $sql;

        $countQuery = $this->em->getConnection()->prepare($countSql);
        $countQuery->execute($params);
        $countResults = $countQuery->fetchAll();

        $dataQuery = $this->em->getConnection()->prepare($dataSql);
        $dataQuery->execute($params);
        $results = $dataQuery->fetchAll();

        return array($countResults[0], $results);
    }

    /**
     * Get the sort order from the options
     *
     * @param array $options
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
     * @param array $optionsToFields
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
