<?php

namespace Olcs\Db\Service\Search;

use Elastica\Query;

/**
 * Class QueryTemplate
 *
 * @package Olcs\Db\Service\Search
 */
class QueryTemplate extends Query
{
    /**
     * QueryTemplate constructor.
     *
     * @param string $filename   Filename
     * @param string $searchTerm Search term
     * @param array  $filters    Filters
     * @param array  $dateRanges Date ranges
     *
     * @return void
     */
    public function __construct($filename, $searchTerm, $filters = [], $dateRanges = [])
    {
        if (!file_exists($filename)) {
            throw new \RuntimeException("Query template file '". $filename ."' is missing");
        }

        $template = str_replace('%SEARCH_TERM%', trim(json_encode($searchTerm), '"'), file_get_contents($filename));

        $this->_params = json_decode($template, true);

        // apply filters
        $this->applyFilters($filters);

        // apply date ranges
        $this->applyDateRanges($dateRanges);
    }

    /**
     * Apply filters
     *
     * @param array $filters Filters
     *
     * @return $this
     */
    private function applyFilters($filters)
    {
        if (empty($filters)) {
            return $this;
        }

        $boolQuery = $this->_params['query']['indices']['query']['bool'];

        if (!isset($boolQuery['must'])) {
            $boolQuery['must'] = [];
        }

        foreach ($filters as $field => $value) {
            if (!empty($field) && !empty($value)) {
                $boolQuery['must'][] = [
                    'match' => [
                        $field => $value
                    ]
                ];
            }
        }

        if (!empty($boolQuery['must'])) {
            $this->_params['query']['indices']['query']['bool'] = $boolQuery;
        }

        return $this;
    }

    /**
     * Apply date ranges
     *
     * @param array $dates Dates
     *
     * @return $this
     */
    private function applyDateRanges($dates)
    {
        if (empty($dates)) {
            return $this;
        }

        $boolQuery = $this->_params['query']['indices']['query']['bool'];

        if (!isset($boolQuery['must'])) {
            $boolQuery['must'] = [];
        }

        foreach ($dates as $fieldName => $value) {
            $lcFieldName = strtolower($fieldName);

            if (substr($lcFieldName, -11) === 'from_and_to') {
                /* from_and_to allows a single date field to be used as a terms filter whilst keeping the
                 * individual Day/Month/Year input fields. The 'from_and_to' is identified and a single date is
                 * added as a query match rather than a range (for efficiency)
                 */
                $fieldName = substr($fieldName, 0, -12);

                $boolQuery['must'][] = [
                    'match' => [
                        $fieldName => $value
                    ]
                ];

            } elseif (substr($lcFieldName, -4) === 'from') {
                $criteria = [];

                $fieldName = substr($fieldName, 0, -5);
                $criteria['from'] = $value;

                // Let's now look for the to field.
                $toFieldName = $fieldName . '_to';

                if (!empty($dates[$toFieldName])) {
                    $criteria['to'] = $dates[$toFieldName];
                }

                $boolQuery['must'][] = [
                    'range' => [
                        $fieldName => $criteria
                    ]
                ];
            }
        }

        if (!empty($boolQuery['must'])) {
            $this->_params['query']['indices']['query']['bool'] = $boolQuery;
        }

        return $this;
    }
}
