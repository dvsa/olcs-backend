<?php

/**
 * Snapshot
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\TransportManagerApplication\Snapshot as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Transfer\Command\Document\Upload;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Generator;

/**
 * Snapshot
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Snapshot extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TransportManagerApplication';

    /**
     * @var Generator
     */
    protected $reviewSnapshotService;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->reviewSnapshotService = $container->get('TmReviewSnapshot');
        return parent::__invoke($container, $requestedName, $options);
    }

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var TransportManagerApplication $tma */
        $tma = $this->getRepo()->fetchUsingId($command);

        $markup = $this->reviewSnapshotService->generate($tma);

        $this->result->addMessage('Snapshot generated');

        $this->result->merge($this->generateDocument($markup, $tma, $command->getUser()));

        return $this->result;
    }

    protected function generateDocument($content, TransportManagerApplication $tma, $user)
    {
        $fileName = sprintf(
            'TM%s snapshot for application %s (at grant).html',
            $tma->getTransportManager()->getId(),
            $tma->getApplication()->getId()
        );

        $data = [
            'content' => base64_encode(trim($content)),
            'filename' => $fileName,
            'category' => Category::CATEGORY_TRANSPORT_MANAGER,
            'subCategory' => Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL,
            'isExternal' => false,
            'isScan' => false,
            'transportManager' => $tma->getTransportManager()->getId(),
            'application' => $tma->getApplication()->getId(),
            'licence' => $tma->getApplication()->getLicence()->getId(),
            'user' => $user
        ];

        return $this->handleSideEffect(Upload::create($data));
    }
}
