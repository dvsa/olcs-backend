<?php

namespace Olcs\Db\Service\Search;

use DomainException;
use Elastica\Query;
use RuntimeException;

/**
 * Class QueryTemplate
 *
 * @package Olcs\Db\Service\Search
 */
class QueryTemplate extends Query
{
    public const FILTER_TYPE_DYNAMIC = 'DYNAMIC';
    public const FILTER_TYPE_BOOLEAN = 'BOOLEAN';

    public function __construct(
        string $filename,
        string $searchTerm,
        array $filters = [],
        array $filterTypes = [],
        $dateRanges = []
    ) {
        if (!file_exists($filename)) {
            throw new RuntimeException("Query template file '" . $filename . "' is missing");
        }

        $searchTermReplace = json_encode($searchTerm);

        if ($searchTermReplace === false) {
            throw new RuntimeException(
                "Search term '" . $searchTerm . "' gives invalid json. Error: " . json_last_error_msg()
            );
        }

        // OLCS-14386 - don't use trim to remove " from the encoded $searchTerm
        $template = str_replace(
            '%SEARCH_TERM%',
            substr(substr($searchTermReplace, 1), 0, -1),
            file_get_contents($filename)
        );

        $this->_params = json_decode($template, true);

        if (empty($this->_params)) {
            throw new RuntimeException(
                "Empty params for query template file '" . $filename . "' and search term '" . $searchTerm . "'"
            );
        }

        // apply filters
        $this->applyFilters($filters, $filterTypes);

        // apply date ranges
        $this->applyDateRanges($dateRanges);
    }

    private function applyFilters(array $filters, array $filterTypes): self
    {
        if (empty($filters)) {
            return $this;
        }

        foreach ($filters as $field => $value) {
            if (empty($field) || $value === '') {
                continue;
            }

            switch ($filterTypes[$field]) {
                case self::FILTER_TYPE_DYNAMIC:
                    $this->_params['query']['bool']['filter'][] = [
                        'term' => [
                            $field => $value,
                        ],
                    ];
                    break;
                case self::FILTER_TYPE_BOOLEAN:
                    if ((int)$value === 1) {
                        $this->_params['query']['bool']['must']['bool']['must'][] = [
                            'exists' => [
                                'field' => $field,
                            ],
                        ];
                    } else {
                        $this->_params['query']['bool']['must_not'][] = [
                            'exists' => [
                                'field' => $field,
                            ],
                        ];
                    }
                    break;
                default:
                    throw new DomainException('Invalid filter type: ' . $filterTypes[$field]);
            }
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

        foreach ($dates as $fieldName => $value) {
            $lcFieldName = strtolower($fieldName);

            if (substr($lcFieldName, -11) === 'from_and_to') {
                /* from_and_to allows a single date field to be used as a terms filter whilst keeping the
                 * individual Day/Month/Year input fields. The 'from_and_to' is identified and a single date is
                 * added as a query match rather than a range (for efficiency)
                 */
                $fieldName = substr($fieldName, 0, -12);

                $this->_params['query']['bool']['filter'][] = [
                    'term' => [
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

                $this->_params['query']['bool']['filter'][] = [
                    'range' => [
                        $fieldName => $criteria
                    ]
                ];
            }
        }

        return $this;
    }
}
