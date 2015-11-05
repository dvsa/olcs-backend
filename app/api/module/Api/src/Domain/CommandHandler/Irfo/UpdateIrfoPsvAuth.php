<?php

/**
 * Update IrfoPsvAuth
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthNumber;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Update IrfoPsvAuth
 */
final class UpdateIrfoPsvAuth extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'IrfoPsvAuth';

    protected $extraRepos = ['IrfoPsvAuthNumber'];

    /**
     * Handle command
     *
     * @param CommandInterface $command
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $irfoPsvAuth = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $irfoPsvAuth->update(
            $this->getRepo()->getReference(IrfoPsvAuthType::class, $command->getIrfoPsvAuthType()),
            $command->getValidityPeriod(),
            new \DateTime($command->getInForceDate()),
            $command->getServiceRouteFrom(),
            $command->getServiceRouteTo(),
            $this->getRepo()->getRefdataReference($command->getJourneyFrequency()),
            $command->getCopiesRequired(),
            $command->getCopiesRequiredTotal()
        );

        if ($command->getExpiryDate() !== null) {
            $irfoPsvAuth->setExpiryDate(new \DateTime($command->getExpiryDate()));
        }

        if ($command->getApplicationSentDate() !== null) {
            $irfoPsvAuth->setApplicationSentDate(new \DateTime($command->getApplicationSentDate()));
        }

        if ($command->getCountrys() !== null) {
            $countries = [];

            foreach ($command->getCountrys() as $countryId) {
                $countries[] = $this->getRepo()->getReference(Country::class, $countryId);
            }

            $irfoPsvAuth->setCountrys($countries);
        }

        if ($command->getIsFeeExemptApplication() !== null) {
            $irfoPsvAuth->setIsFeeExemptApplication($command->getIsFeeExemptApplication());
        }

        if ($command->getIsFeeExemptAnnual() !== null) {
            $irfoPsvAuth->setIsFeeExemptAnnual($command->getIsFeeExemptAnnual());
        }

        if ($command->getExemptionDetails() !== null) {
            $irfoPsvAuth->setExemptionDetails($command->getExemptionDetails());
        }

        $this->getRepo()->save($irfoPsvAuth);

        // deal with IrfoPsvAuthNumbers
        $this->processIrfoPsvAuthNumbers($irfoPsvAuth, $command->getIrfoPsvAuthNumbers());

        $result = new Result();
        $result->addId('irfoPsvAuth', $irfoPsvAuth->getId());
        $result->addMessage('IRFO PSV Auth updated successfully');

        return $result;
    }

    /**
     * @param IrfoPsvAuth $irfoPsvAuth
     * @param array $irfoPsvAuthNumbers
     * @return array
     */
    private function processIrfoPsvAuthNumbers(IrfoPsvAuth $irfoPsvAuth, array $irfoPsvAuthNumbers)
    {
        $reduced = [];

        foreach ($irfoPsvAuthNumbers as $irfoPsvAuthNumber) {
            if (empty($irfoPsvAuthNumber['name'])) {
                // filter out empty values
                continue;
            }

            if (!empty($irfoPsvAuthNumber['id'])) {
                // update
                $irfoPsvAuthNumberEntity = $this->getRepo('IrfoPsvAuthNumber')->fetchById(
                    $irfoPsvAuthNumber['id'],
                    Query::HYDRATE_OBJECT,
                    $irfoPsvAuthNumber['version']
                );
                $irfoPsvAuthNumberEntity->setName($irfoPsvAuthNumber['name']);
            } else {
                // create
                $irfoPsvAuthNumberEntity = new IrfoPsvAuthNumber(
                    $irfoPsvAuth,
                    $irfoPsvAuthNumber['name']
                );
            }

            $this->getRepo('IrfoPsvAuthNumber')->save($irfoPsvAuthNumberEntity);
            $reduced[] = $irfoPsvAuthNumberEntity->getId();
        }

        // remove the rest records
        foreach ($irfoPsvAuth->getIrfoPsvAuthNumbers() as $irfoPsvAuthNumberEntity) {
            if (!in_array($irfoPsvAuthNumberEntity->getId(), $reduced)) {
                $this->getRepo('IrfoPsvAuthNumber')->delete($irfoPsvAuthNumberEntity);
            }
        }

        return $reduced;
    }
}
