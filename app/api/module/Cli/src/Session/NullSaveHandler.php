<?php

namespace Dvsa\Olcs\Cli\Session;

/**
 * NullSaveHandler, blocks he writing of session data
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class NullSaveHandler implements \Laminas\Session\SaveHandler\SaveHandlerInterface
{
    /**
     * Open Session - retrieve resources
     *
     * @param string $savePath
     * @param string $name
     */
    public function open($savePath, $name)
    {
        return true;
    }

    /**
     * Close Session - free resources
     *
     */
    public function close()
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id
     */
    public function read($id)
    {
        // PHP 7.1 session handlers returning a bool or null value causes errors in logs. Empty string does not.
        return '';
    }

    /**
     * Write Session - commit data to resource
     *
     * @param string $id
     * @param mixed $data
     */
    public function write($id, $data)
    {
        return true;
    }

    /**
     * Destroy Session - remove data from resource for
     * given session id
     *
     * @param string $id
     */
    public function destroy($id)
    {
        return true;
    }

    /**
     * Garbage Collection - remove old session data older
     * than $maxlifetime (in seconds)
     *
     * @param int $maxlifetime
     */
    public function gc($maxlifetime)
    {
        return true;
    }
}
