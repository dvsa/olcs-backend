<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as EbsrSubmissionRepo;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * OrganisationUnprocessedList
 *
 * Brings back a list of unprocessed ebsr submissions for an organisation
 */
class OrganisationUnprocessedList extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'EbsrSubmission';

    /**
     * @param QueryInterface $query
     * @return array
     * @throws ValidationException
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var EbsrSubmissionRepo $repo */
        $repo = $this->getRepo();

        $organisation = $this->getCurrentOrganisation();

        if (!$organisation instanceof Organisation) {
            throw new ValidationException(['No organisation was found']);
        }

        $results = $repo->fetchForOrganisationWithDocs($organisation->getId(), EbsrSubmission::SUBMITTING_STATUS);

        $documents = [];
        
        /** @var EbsrSubmission $ebsrSub */
        foreach ($results as $ebsrSub) {
            /** @var Document $document */
            $document = $ebsrSub->getDocument();

            //document may have been deleted from the document store
            if ($document instanceof Document) {
                $documents[] = $document;
            }
        }

        return $this->resultList($documents);
    }
}
