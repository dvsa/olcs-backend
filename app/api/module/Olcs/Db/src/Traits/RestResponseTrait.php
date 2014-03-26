<?php

namespace Olcs\Db\Traits;
use Zend\Http\Response;

trait RestResponseTrait
{
    /**
     * Create a new instance of Response
     *
     * @return Response
     */
    public function getNewResponse()
    {
        return new Response();
    }

    /**
     * Creates a response object and set's up the response body
     *
     * @param int $errorCode
     * @param string $summary
     * @param array $data
     * @return Response
     */
    public function respond($errorCode, $summary = null, $data = array())
    {
        $response = $this->getNewResponse();

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
