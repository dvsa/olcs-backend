<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Adapter;

use Laminas\Authentication\Adapter\ValidatableAdapterInterface;

class MockAdapter implements ValidatableAdapterInterface
{
    public function authenticate()
    {
    }
    public function getIdentity()
    {
    }
    public function setIdentity($identity)
    {
    }
    public function getCredential()
    {
    }
    public function setCredential($credential)
    {
    }
}
