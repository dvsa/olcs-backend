<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Service\OpenAm\Callback;

class Request
{
    public const STAGE_AUTHENTICATE = 'LDAP1';
    public const STAGE_EXPIRED_PASSWORD = 'LDAP2';

    /**
     * @var string
     */
    private $authId;

    /**
     * @var string
     */
    private $stage;

    /**
     * @var CallbackInterface[]|array
     */
    private $callbacks = [];

    /**
     * Create a request
     *
     * @param string                    $authId    Auth id
     * @param string                    $stage     Stage
     * @param CallbackInterface[]|array $callbacks Callbacks
     *
     * @return void
     */
    public function __construct(string $authId, string $stage, array $callbacks = [])
    {
        $this->authId = $authId;
        $this->stage = $stage;
        $this->callbacks = $callbacks;
    }

    /**
     * Add callback
     *
     * @param CallbackInterface $callback Callback
     *
     * @return void
     */
    public function addCallback(CallbackInterface $callback): void
    {
        $this->callbacks[] = $callback;
    }

    /**
     * Convert object to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $callbacks = [];

        /** @var CallbackInterface $callback */
        foreach ($this->callbacks as $callback) {
            $callbacks[] = $callback->toArray();
        }

        return [
            'authId' => $this->authId,
            'stage' => $this->stage,
            'callbacks' => $callbacks
        ];
    }
}
