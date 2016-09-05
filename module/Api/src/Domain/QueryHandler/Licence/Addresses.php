<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query as TransferQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use \Dvsa\Olcs\Api\Entity;

/**
 * Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class Addresses extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';
    protected $extraRepos = ['PhoneContact'];

    /**
     * Process query
     *
     * @param TransferQry\Licence\Addresses $query Query
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var \Dvsa\Olcs\Api\Domain\Repository\Licence $licenceRepo */
        $licenceRepo = $this->getRepo();

        /** @var Entity\Licence\Licence $licence */
        $licence = $licenceRepo->fetchWithAddressesUsingId($query);

        return $this->result(
            $licence,
            [
                'correspondenceCd' => [
                    'address' => [
                        'countryCode',
                    ],
                    'contactType',
                ],
                'establishmentCd' => [
                    'address' => [
                        'countryCode',
                    ],
                    'contactType',
                ],
                'transportConsultantCd' => [
                    'address' => [
                        'countryCode',
                    ],
                    'contactType',
                    'phoneContacts' => [
                        'phoneContactType',
                    ],
                ],
            ],
            [
                'correspondenceCd' => $this->corrCrValues($licence),
            ]
        );
    }

    /**
     * Additional values of correspondence contact details
     *
     * @param Entity\Licence\Licence $licence Licence entity
     *
     * @return array
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function corrCrValues(Entity\Licence\Licence $licence)
    {
        if (null === $licence->getCorrespondenceCd()) {
            return [];
        }

        //  get phone contacts
        $qryPhoneContacts = TransferQry\ContactDetail\PhoneContact\GetList::create(
            [
                'contactDetailsId' => $licence->getCorrespondenceCd()->getId(),
                'sort' => '_type, phoneNumber',
            ]
        );

        $phoneContacts = $this->getRepo('PhoneContact')->fetchList($qryPhoneContacts, Query::HYDRATE_OBJECT);

        return [
            'phoneContacts' => $this->resultList($phoneContacts, ['phoneContactType']),
        ];
    }
}
