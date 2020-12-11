<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\BulkSend;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\AbstractEmailHandler;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Template\Template;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Handle email from bulk report upload
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class ProcessEmail extends AbstractEmailHandler
{
    protected $repoServiceName = 'Licence';

    /**
     * Sends email to licence specifed on bulk upload sheet.
     *
     * @param CommandInterface $command
     *
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Dvsa\Olcs\Email\Exception\EmailNotSentException
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->template = $command->getTemplateName();
        $this->subject = $this->getSubjectLine();
        return parent::handleCommand($command);
    }

    /**
     * Returns a string to use as subject line for email
     *
     * @return string
     */
    private function getSubjectLine()
    {
        return
            array_key_exists($this->template, Template::BULK_TEMPLATE_SUBJECT_MAP)
                ? Template::BULK_TEMPLATE_SUBJECT_MAP[$this->template]
                : 'Important information about your vehicle operator licence';
    }

    /**
     * Get template variables
     *
     * @param Licence $recordObject
     *
     * @return array
     */
    protected function getTemplateVariables($recordObject): array
    {
        $vars = [
            'licenceType' => $recordObject->getLicenceType()->getDescription(),
            'goodsOrPsv' => $recordObject->getGoodsOrPsv()->getDescription()
        ];

        return $vars;
    }

    protected function getRecipients($recordObject): array
    {
        return $this->organisationRecipients($recordObject->getOrganisation());
    }

    /**
     * {@inheritdoc}
     */
    protected function getTranslateToWelsh($recordObject)
    {
        return $recordObject->getTranslateToWelsh();
    }
}
