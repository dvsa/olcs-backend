<?php

/**
 * Cpms Report Status
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cpms;

use Dvsa\Olcs\Api\Domain\CpmsAwareInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Cpms Report Status
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ReportStatus extends AbstractQueryHandler implements CpmsAwareInterface
{
    use CpmsAwareTrait;

    public function handleQuery(QueryInterface $query)
    {
        $result = [
            'completed' => false,
        ];

        $data = $this->getCpmsService()->getReportStatus($query->getReference());

        if (isset($data['completed']) && $data['completed']) {
            $result = [
                // we only really need to return token and extension, we
                // already know what the download url will be based on the reference
                'completed' => $data['completed'],
                'token' => $this->parseToken($data),
                'extension' => isset($data['file_extension']) ? $data['file_extension'] : null,
            ];
        }

        // @todo can remove this
        $result['debug'] = $data;

        return $result;
    }

    /**
     * @param array $data
     * @return string
     */
    private function parseToken($data)
    {
        $downloadUrl = $data['download_url'];
        $queryString = parse_url($downloadUrl, PHP_URL_QUERY);
        $vars = [];
        parse_str($queryString, $vars);
        return $vars['token'];
    }
}
