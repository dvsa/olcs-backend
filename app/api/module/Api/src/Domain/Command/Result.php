<?php

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

    /**
     * @var array
     */
    private $flags = [];

    /**
     * @param string $name
     * @param int $id
     * @param boolean $multiple whether to allow multiple IDs of same type,
     * the default behaviour is that subsequent IDs just override the first one
     */
    public function addId($name, $id, $multiple = false)
    {
        if ($multiple) {
            if (isset($this->ids[$name])) {
                if (!is_array($this->ids[$name])) {
                    $current = $this->ids[$name];
                    $this->ids[$name] = array($current);
                }
                array_push($this->ids[$name], $id);
            } else {
                $this->ids[$name] = $id;
            }

            return $this;
        }

        $this->ids[$name] = $id;

        return $this;
    }

    public function getId($name)
    {
        return isset($this->ids[$name]) ? $this->ids[$name] : null;
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
        return $this;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    public function setFlag($name, $value)
    {
        $this->flags[$name] = $value;
    }

    public function getFlag($name)
    {
        return $this->flags[$name];
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
