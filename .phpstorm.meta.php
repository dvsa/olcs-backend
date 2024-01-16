<?php

namespace PHPSTORM_META {
    override(
        \Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler::getRepo(0),
        map([
            '' => '\Dvsa\Olcs\Api\Domain\Repository\@',
            '' => '@'
        ])
    );
}
