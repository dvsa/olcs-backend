<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Standard;

return [
    CommandHandler\Application\CreateGoodsVehicle::class                        => Standard::class, // @todo
    CommandHandler\Application\CreatePsvVehicle::class                          => Standard::class, // @todo
    CommandHandler\Application\CreateVehicleListDocument::class                 => Standard::class, // @todo
    CommandHandler\Application\DeleteGoodsVehicle::class                        => Standard::class, // @todo
    CommandHandler\Application\DeletePsvVehicle::class                          => Standard::class, // @todo
    CommandHandler\Application\UpdateGoodsVehicle::class                        => Standard::class, // @todo
    CommandHandler\Application\UpdatePsvVehicles::class                         => Standard::class, // @todo
    CommandHandler\Application\UpdateVehicleDeclaration::class                  => Standard::class, // @todo
    CommandHandler\Application\UpdateVehicles::class                            => Standard::class, // @todo
    CommandHandler\LicenceVehicle\CreateUnlicensedOperatorLicenceVehicle::class => Standard::class, // @todo
    CommandHandler\LicenceVehicle\DeleteUnlicensedOperatorLicenceVehicle::class => Standard::class, // @todo
    CommandHandler\LicenceVehicle\UpdatePsvLicenceVehicle::class                => Standard::class, // @todo
    CommandHandler\LicenceVehicle\UpdateUnlicensedOperatorLicenceVehicle::class => Standard::class, // @todo
    CommandHandler\Licence\CreateGoodsVehicle::class                            => Standard::class, // @todo
    CommandHandler\Licence\CreatePsvVehicle::class                              => Standard::class, // @todo
    CommandHandler\Licence\CreateVehicleListDocument::class                     => Standard::class, // @todo
    CommandHandler\Licence\TransferVehicles::class                              => Standard::class, // @todo
    CommandHandler\Licence\UpdateVehicles::class                                => Standard::class, // @todo
    CommandHandler\Vehicle\DeleteLicenceVehicle::class                          => Standard::class, // @todo
    CommandHandler\Vehicle\ReprintDisc::class                                   => Standard::class, // @todo
    CommandHandler\Vehicle\UpdateGoodsVehicle::class                            => Standard::class, // @todo
    CommandHandler\Vehicle\UpdateSection26::class                               => Standard::class, // @todo
    QueryHandler\Application\GoodsVehicles::class                               => Standard::class, // @todo
    QueryHandler\Application\PsvVehicles::class                                 => Standard::class, // @todo
    QueryHandler\Application\VehicleDeclaration::class                          => Standard::class, // @todo
    QueryHandler\LicenceVehicle\LicenceVehicle::class                           => Standard::class, // @todo
    QueryHandler\LicenceVehicle\PsvLicenceVehicle::class                        => Standard::class, // @todo
    QueryHandler\Licence\GoodsVehicles::class                                   => Standard::class, // @todo
    QueryHandler\Licence\PsvVehicles::class                                     => Standard::class, // @todo
    QueryHandler\Operator\UnlicensedVehicles::class                             => Standard::class, // @todo
    QueryHandler\Variation\GoodsVehicles::class                                 => Standard::class, // @todo
    QueryHandler\Variation\PsvVehicles::class                                   => Standard::class, // @todo
];
