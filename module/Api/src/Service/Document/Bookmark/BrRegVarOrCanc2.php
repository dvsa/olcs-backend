<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

/**
 * BrRegVarOrCanc2 Bookmark
 */
class BrRegVarOrCanc2 extends BrRegVarOrCanc
{
    protected $renderNew = 'REGISTER';
    protected $renderVar = 'VARY';
    protected $renderCanc = 'CANCEL';
}
