<?php

/**
 * Paginate Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Utility;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Olcs\Db\Exceptions\PaginationException;

/**
 * Paginate Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PaginateQuery implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $qb;
    protected $page = 1;
    protected $limit = 10;
    protected $order = 'ASC';
    protected $sort;


    public function __construct($qb)
    {
        $this->qb = $qb;
    }

    public function setOptions(array $options = array())
    {
        if (isset($options['page'])) {

            if (!is_numeric($options['page'])) {
                throw new PaginationException('Page number must be numeric');
            }

            $this->page = (int)$options['page'];
        }

        if (isset($options['limit'])) {

            if ($options['limit'] === 'all') {
                $this->limit = null;
            } elseif (!is_numeric($options['limit'])) {
                throw new PaginationException('Search limit must be numeric');
            } else {
                $this->limit = (int)$options['limit'];
            }
        }

        if (isset($options['order'])) {

            if (!in_array($options['order'], ['ASC', 'DESC'])) {
                throw new PaginationException('Order must be either ASC or DESC');
            }

            $this->order = $options['order'];
        }

        if (isset($options['sort'])) {
            $this->sort = $options['sort'];
        }
    }

    public function filterQuery()
    {
        if ($this->limit !== null) {
            $this->qb->setFirstResult($this->getOffset($this->page, $this->limit));
            $this->qb->setMaxResults($this->limit);
        }

        if ($this->sort !== null) {
            $this->qb->orderBy('m.' . $this->sort, $this->order);
        }
    }

    /**
     * Get the offset value
     *
     * @param int $page
     * @param int $limit
     * @return type
     */
    protected function getOffset($page, $limit)
    {
        return ($page * $limit) - $limit;
    }
}
