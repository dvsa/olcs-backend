<?php

/**
 * Command Result
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command;

/**
 * Command Result
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Result
{
    /**
     * @var array
     */
    private $ids = [];

    /**
     * @var array
     */
    private $messages = [];

    public function addId($name, $id)
    {
        $this->ids[$name] = $id;
    }

    public function getId($name)
    {
        return $this->ids[$name];
    }

    /**
     * @return array
     */
    public function getIds()
    {
        return $this->ids;
    }

    public function addMessage($message)
    {
        $this->messages[] = $message;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    public function merge(Result $result)
    {
        $this->ids = array_merge($this->ids, $result->getIds());
        $this->messages = array_merge($this->messages, $result->getMessages());
    }

    public function toArray()
    {
        return [
            'id' => $this->ids,
            'messages' => $this->messages
        ];
    }
}
