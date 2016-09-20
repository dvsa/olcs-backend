<?php

namespace Olcs\Db\Service\Search;

use Elastica\Query;

class QueryTemplate extends Query
{
    /**
     * QueryTemplate constructor.
     *
     * @param $filename
     */
    public function __construct($filename, $searchTerm)
    {
        if (!file_exists($filename)) {
            throw new \RuntimeException("Query template file '". $filename ."' is missing");
        }

        $template = str_replace('"%SEARCH_TERM%"', json_encode($searchTerm), file_get_contents($filename));

        $this->_params = json_decode($template, true);
    }
}
