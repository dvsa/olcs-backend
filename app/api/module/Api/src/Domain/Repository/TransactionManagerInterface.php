<?php
namespace Dvsa\Olcs\Api\Domain\Repository;


/**
 * Abstract Repository
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface TransactionManagerInterface
{
    public function beginTransaction();

    public function commit();

    public function rollback();
}