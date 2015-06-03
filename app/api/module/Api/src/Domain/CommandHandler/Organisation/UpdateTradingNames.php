<?php

/**
 * Update Trading Names
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Organisation;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Doctrine\Common\Collections\Collection;
use Dvsa\Olcs\Api\Entity\Organisation\TradingName;

/**
 * Update Trading Names
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateTradingNames extends AbstractCommandHandler
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['Organisation', 'TradingName'];

    public function handleCommand(CommandInterface $command)
    {
        if ($command->getLicence() !== null) {
            /** @var Licence $licence */
            $licence = $this->getRepo()->fetchById($command->getLicence(), Query::HYDRATE_OBJECT);
            $organisation = $licence->getOrganisation();
            $current = $licence->getTradingNames();
        } else {
            $licence = null;
            /** @var Organisation $organisation */
            $organisation = $this->getRepo('Organisation')
                ->fetchById($command->getOrganisation(), Query::HYDRATE_OBJECT);

            $criteria = Criteria::create();
            $criteria->where($criteria->expr()->isNull('licence'));

            $current = $organisation->getTradingNames()->matching($criteria);
        }

        $result = new Result();

        if ($this->haveTradingNamesChanged($current, $command->getTradingNames())) {

            try {
                $this->getRepo()->beginTransaction();

                $result->setFlag('hasChanged', true);

                list($newCount, $unchangedCount, $removedCount) = $this->updateTradingNames(
                    $current,
                    $command->getTradingNames(),
                    $organisation,
                    $licence
                );

                $result->addMessage($newCount . ' new trading name(s)');
                $result->addMessage($unchangedCount . ' unchanged trading name(s)');
                $result->addMessage($removedCount . ' trading name(s) removed');

                $this->getRepo()->commit();
            } catch (\Exception $ex) {
                $this->getRepo()->rollback();
                throw $ex;
            }

        } else {
            $result->setFlag('hasChanged', false);
            $result->addMessage('Trading names are unchanged');
        }

        return $result;
    }

    private function updateTradingNames(
        Collection $current,
        array $new,
        Organisation $organisation,
        Licence $licence = null
    ) {
        // Differentiate between trading names to keep and trading names to remove
        list($maintain, $remove) = $current->partition(function ($key, $tradingName) use (&$new) {

            $index = array_search($tradingName->getName(), $new);

            if ($index !== false) {

                unset($new[$index]);

                return true;
            }

            return false;
        });

        $newCount = 0;
        $unchangedCount = $maintain->count();
        $removedCount = $remove->count();

        // Remove trading names
        foreach ($remove as $key => $entity) {
            $this->getRepo('TradingName')->delete($entity);
        }

        // Create new trading names
        foreach ($new as $name) {
            $newCount++;
            $this->createTradingName($name, $organisation, $licence);
        }

        return [$newCount, $unchangedCount, $removedCount];
    }

    private function createTradingName($name, Organisation $organisation, Licence $licence = null)
    {
        $tradingName = new TradingName($name, $organisation);

        if ($licence !== null) {
            $tradingName->setLicence($licence);
        }

        $this->getRepo('TradingName')->save($tradingName);

        return $tradingName;
    }

    private function haveTradingNamesChanged(Collection $current, array $new)
    {
        // We don't have the same number, so we must have changes
        if ($current->count() != count($new)) {
            return true;
        }

        // Check if all new names exist
        foreach ($new as $name) {
            $matched = false;

            /** @var TradingName $tradingName */
            foreach ($current as $tradingName) {

                if ($tradingName->getName() === $name) {
                    $matched = true;
                    break;
                }
            }

            // We have at least 1 new trading name
            if ($matched === false) {
                return true;
            }
        }

        return false;
    }
}
