<?php

namespace Dvsa\Olcs\DvsaAddressService\Client;

use Dvsa\Olcs\DvsaAddressService\Exception\ServiceException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use InvalidArgumentException;

class DvsaAddressServiceClient
{
    protected const URI_SEARCH = '/search';

    public function __construct(private readonly Client $client)
    {

    }

    /**
     * @throws ServiceException|GuzzleException
     */
    public function lookupAddress(string $query): array
    {
        try {
            $response = $this->client->get(static::URI_SEARCH, [
                'query' => ['query' => $query]
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException | ServerException $exception) {
            throw new ServiceException(
                'There was a client/server exception when communicating with the DVSA Address Service API',
                0,
                $exception
            );
        } catch (ConnectException | RequestException $exception) {
            throw new ServiceException(
            'There was an error when communicating with the DVSA Address Service API',
            0,
            $exception
            );
        } catch (InvalidArgumentException $invalidArgumentException) {
            throw new ServiceException(
                'There was an error when JSON decoding the response from DVSA Address Service API',
                0,
                $invalidArgumentException
            );
        }
    }
}
