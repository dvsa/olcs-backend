<?php

namespace Olcs\Db\Traits;
use Zend\Http\Response;

trait RestResponseTrait
{
    /**
     * Creates a response object and set's up the response body
     *
     * @param int $errorCode
     * @param string $summary
     * @param array $data
     * @return Response
     */
    protected function respond($errorCode, $summary = null, $data = array())
    {
        $response = new Response();

        $response->setStatusCode($errorCode);

        $response->setContent(
            json_encode(
                array(
                    'Response' => array(
                        'Code' => $errorCode,
                        'Message' => $response->getReasonPhrase(),
                        'Summary' => $summary,
                        'Data' => $data
                    )
                )
            )
        );

        return $response;
    }
}
