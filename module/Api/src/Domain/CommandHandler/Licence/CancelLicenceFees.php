<?php

/**
 * Cancel Licence Fees
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Cancel Licence Fees
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CancelLicenceFees extends AbstractCommandHandler
{
    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->in('feeStatus', [Fee::STATUS_OUTSTANDING, Fee::STATUS_WAIVE_RECOMMENDED])
        );

        $fees = $licence->getFees()->matching($criteria);

        if (empty($fees)) {
            $result->addMessage('No fees to remove');
            return $result;
        }

        $status = $this->getRepo()->getRefdataReference(Fee::STATUS_CANCELLED);

        /** @var Fee $fee */
        foreach ($fees as $fee) {
            $fee->setFeeStatus($status);
        }

        $this->getRepo()->save($licence);

        $result->addMessage(count($fees) . ' fee(s) cancelled');
        return $result;
    }
}
