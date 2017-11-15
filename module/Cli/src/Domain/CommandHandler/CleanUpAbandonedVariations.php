<?php

/**
 * Clean up abandoned variations
 */
namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Variation\DeleteVariation;

/**
 * Remove abandoned variations
 */

final class CleanUpAbandonedVariations extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

    protected $olderThan = '4 hours';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $config = $mainServiceLocator->get('Config');

        if (isset($config['batch_config']['clean-abandoned-variations']['older-than'])) {
            $this->olderThan = $config['batch_config']['clean-abandoned-variations']['older-than'];
        }

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        $olderThanDate = date('Y-m-d H:i:s', strtotime('-' . $this->olderThan));

        /** @var Application $repo */
        $repo = $this->getRepo();

        $abandonedVariations = $repo->fetchAbandonedVariations($olderThanDate);

        /* @var $variation ApplicationEntity */
        foreach ($abandonedVariations as $variation) {
            $this->handleSideEffect(DeleteVariation::create(['id' => $variation->getId()]));
        }

        $this->result->addMessage(count($abandonedVariations) . ' abandoned variation records deleted');

        return $this->result;
    }
}
