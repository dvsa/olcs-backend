<?php

/**
 * Queue request to update TM name with Nysiis values
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Tm;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreateQueue;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Zend\Serializer\Adapter\Json as ZendJson;

use Dvsa\Olcs\Transfer\Command\Tm\UpdateNysiisName as UpdateNysiisNameCmd;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;

/**
 * Update TM NYSIIS name
 */
final class UpdateNysiisName extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'TransportManager';

    /**
     * Command to queue a request to update TM with Nysiis data
     *
     * @param CommandInterface $command
     *
     * @return Result
     * @throws NotFoundException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var TransportManager $transportManager
         * @var UpdateNysiisNameCmd $command
         */
        $transportManager = $this->getRepo()->fetchUsingId($command);
        $personDetails = $transportManager->getHomeCd()->getPerson();

        if (empty($personDetails)) {
            throw new NotFoundException('The specified TM doesn\'t have an associated person');
        }

        $nysiisData = $this->requestNyiisData(
            [
                'forename' => $personDetails->getForename(),
                'familyName' => $personDetails->getFamilyName()
            ]
        );

        $transportManager->setNysiisForename($nysiisData['forename']);
        $transportManager->setNysiisFamilyName($nysiisData['familyName']);

        $this->getRepo('TransportManager')->save($transportManager);

        $this->result->addMessage('TM NYIIS name was requested and updated');

        return $this->result;
    }

    /**
     * Connect to Nysiis with given params and return values returned by Nysiis
     *
     * @param $params
     * @return mixed
     */
    private function requestNyiisData($params)
    {
        // @to-do  Make request to Nysiis

        // connect to Nysiis here and return whatever Nysiis returns
        return [
            'forename' => $params['firstName'],
            'familyName' => $params['familyName']
        ];
    }
}
