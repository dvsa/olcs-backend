<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;
use Dvsa\Olcs\Api\Domain\TranslatorAwareInterface;
use Dvsa\Olcs\Api\Domain\TranslatorAwareTrait;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * VehiclesSpecified bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class UserAccess extends DynamicBookmark implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    public function getQuery(array $data)
    {
        $bundle = [
            'organisation' => [
                'organisationUsers' => [
                    'user' => [
                        'contactDetails' => [
                            'person'
                        ],
                        'roles'
                    ],
                ]
            ],
        ];
        return Qry::create(['id' => $data['licence'], 'bundle' => $bundle]);
    }

    public function render()
    {
        if (!$this->validate()) {
            return '';
        }

        //TODO: Add additional checks - online access / email registered etc.

        $header[] = [
            'BOOKMARK1' => 'Name',
            'BOOKMARK2' => 'Email address',
            'BOOKMARK3' => 'Permission'
        ];

        $rows = [];
        $organisationUsers = $this->data['organisation']['organisationUsers'];
        foreach ($organisationUsers as $organisationUser) {
            $user = $organisationUser['user'];
            $email = $user['contactDetails']['emailAddress'] ?? '';
            $forename = $user['contactDetails']['person']['forename'] ?? '';
            $familyName = $user['contactDetails']['person']['familyName'] ?? '';
            $name = trim($forename . ' ' . $familyName);
            $permission = implode(
                ',',
                array_map(
                    function ($role) {
                        return $this->getTranslator()->translate('role.'.$role['role'], 'snapshot');
                    },
                    $user['roles']
                )
            );

            $rows[] = [
                'BOOKMARK1' => $name,
                'BOOKMARK2' => $email,
                'BOOKMARK3' => $permission,
            ];
        }
        $snippet = $this->getSnippet('CHECKLIST_3CELL_TABLE');

        $sortUsers = $this->sortUsers($rows);

        $rows = array_pad($sortUsers, 15, ['BOOKMARK1' => '', 'BOOKMARK2' => '', 'BOOKMARK3' => '']);

        $allRows = array_merge($header, $rows);
        $parser  = $this->getParser();

        $str = '';
        foreach ($allRows as $tokens) {
            $str .= $parser->replace($snippet, $tokens);
        }
        return $str;
    }

    protected function sortUsers($rows)
    {
        usort(
            $rows,
            function ($a, $b) {
                if ($a['BOOKMARK1'] == $b['BOOKMARK1']) {
                    return 0;
                } elseif ($a['BOOKMARK1'] < $b['BOOKMARK1']) {
                    return -1;
                } else {
                    return 1;
                }
            }
        );
        return $rows;
    }

    private function validate(): bool
    {
        if (empty($this->data)) {
            return false;
        }

        if (empty($this->data['organisation']['organisationUsers'])) {
            return false;
        }

        return true;
    }
}
