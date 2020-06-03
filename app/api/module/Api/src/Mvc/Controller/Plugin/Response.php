<?php

namespace Dvsa\Olcs\Api\Mvc\Controller\Plugin;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result as QueryResult;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\JsonModel;
use Olcs\Logging\Log\Logger;

/**
 * Response
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Response extends AbstractPlugin
{
    /**
     * Not found
     *
     * @return HttpResponse
     */
    public function notFound()
    {
        /** @var HttpResponse $response */
        $response = $this->getController()->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_404);

        return $response;
    }

    /**
     * Not ready
     *
     * @param int $retryAfter Number of seconds
     *
     * @return HttpResponse
     */
    public function notReady($retryAfter = null)
    {
        /** @var HttpResponse $response */
        $response = $this->getController()->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_503);

        if ($retryAfter) {
            $response->getHeaders()->addHeaders(['Retry-After' => $retryAfter]);
        }

        return $response;
    }

    /**
     * Error
     *
     * @param int   $code     Status code
     * @param array $messages Messages
     *
     * @return JsonModel|HttpResponse
     */
    public function error($code, array $messages = [])
    {
        /** @var HttpResponse $response */
        $response = $this->getController()->getResponse();
        $response->setStatusCode($code);

        if (empty($messages)) {
            return $response;
        }

        return new JsonModel(['messages' => $messages]);
    }

    /**
     * Single result
     *
     * @param QueryResult|Result $result Result
     *
     * @return JsonModel|HttpResponse\Stream
     */
    public function singleResult($result)
    {
        /** @var HttpResponse $response */
        $response = $this->getController()->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_200);

        if ($result instanceof HttpResponse\Stream) {
            return $result;
        }

        if ($result instanceof QueryResult) {
            $result = $result->serialize();
        }

        if (!is_array($result) && $result instanceof \JsonSerializable) {
            Logger::info('Using legacy jsonSerialize on ' . get_class($result));
            $result = $result->jsonSerialize();
        }

        return new JsonModel($result);
    }

    /**
     * Multiple results
     *
     * @param int   $count           Count
     * @param array $results         Results
     * @param int   $countUnfiltered Unfiltered count
     * @param array $extra           Extra values
     *
     * @return JsonModel
     */
    public function multipleResults($count, $results, $countUnfiltered = 0, array $extra = [])
    {
        /** @var HttpResponse $response */
        $response = $this->getController()->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_200);

        return new JsonModel(
            [
                'count' => $count,
                'results' => $results,
                'count-unfiltered' => $countUnfiltered,
                'extra' => $extra
            ]
        );
    }

    /**
     * Echo stream (used for download, because SimpleStreamResponseSender fail on big
     * files @see \Zend\Mvc\ResponseSender\SimpleStreamResponseSender
     *
     * @param HttpResponse\Stream $result File Stream
     *
     * @return bool
     */
    public function streamResult(HttpResponse\Stream $result)
    {
        foreach ($result->getHeaders() as $header) {
            header($header->toString());
        }

        $bh = fopen('php://output', 'w+b');
        stream_copy_to_stream($result->getStream(), $bh);

        fclose($bh);
        fclose($result->getStream());

        return false;
    }

    /**
     * Successful update
     *
     * @param QueryResult|Result $result Result
     *
     * @return JsonModel
     */
    public function successfulUpdate(Result $result)
    {
        /** @var HttpResponse $response */
        $response = $this->getController()->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_200);

        return new JsonModel($result->toArray());
    }

    /**
     * Successful create
     *
     * @param QueryResult|Result $result Result
     *
     * @return JsonModel
     */
    public function successfulCreate(Result $result)
    {
        /** @var HttpResponse $response */
        $response = $this->getController()->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_201);

        return new JsonModel($result->toArray());
    }

    /**
     * We want to answer with accepted code but not to provide other information, as this is going externally
     *
     * @return HttpResponse
     */
    public function xmlAccepted()
    {
        $response = new HttpResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_202);

        return $response;
    }

    /**
     * We want to answer with a bad request code but not provide error messages, as this is going externally
     *
     * @return HttpResponse
     */
    public function xmlBadRequest()
    {
        $response = new HttpResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_400);

        return $response;
    }
}
