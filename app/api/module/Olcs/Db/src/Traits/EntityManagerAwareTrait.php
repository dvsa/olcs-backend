<?php
namespace Olcs\Db\Traits;

use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerAwareTrait
{
    /**
     * Contains an instance of the Doctrine 2.x entity manager.
     *
     * @var EntityManagerInterface
     */
    protected $em = null;

    /**
     * Returns and instance of the Doctrine 2.x entity manager.
     *
     * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
     * @since  2014-01-13
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            throw new \LogicException('Entity manager must be set before it can be requested.');
        }

        return $this->em;
    }

    /**
     * Set the Doctrine 2.x entity manager.
     *
     * @param EntityManagerInterface $em
     * @return EntityManagerAwareTrait
     */
    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->em = $em;

        return $this;
    }

    /**
     * Calls the entity manager and persist.
     *
     * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
     * @since  2014-02-04
     * @return EntityManagerAwareTrait
     */
    public function dbPersist($entity)
    {
        $this->getEntityManager()->persist($entity);
        return $this;
    }

    /**
     * Calls the entity manager and start transaction.
     *
     * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
     * @since  2014-02-04
     * @return EntityManagerAwareTrait
     */
    public function dbStartTransaction()
    {
        $this->getEntityManager()->getConnection()->beginTransaction();
        return $this;
    }

    /**
     * Calls the entity manager and commit.
     *
     * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
     * @since  2014-02-04
     * @return EntityManagerAwareTrait
     */
    public function dbCommit()
    {
        $this->getEntityManager()->commit();
        return $this;
    }

    /**
     * Calls the entity manager and flush.
     *
     * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
     * @since  2014-02-04
     * @return EntityManagerAwareTrait
     */
    public function dbFlush()
    {
        $this->getEntityManager()->flush();
        return $this;
    }

    /**
     * Calls the entity manager and rolback.
     *
     * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
     * @since  2014-02-04
     * @return EntityManagerAwareTrait
     */
    public function dbRollback()
    {
        $this->getEntityManager()->getConnection()->rollback();
        $this->getEntityManager()->close();
        return $this;
    }
}
