<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'doctrine' => array(

        // Configuration details for the ORM.
        // See http://docs.doctrine-project.org/en/latest/reference/configuration.html

        'configuration' => array(

            'orm_default' => array(

                // directory where proxies will be stored. We are
                'proxy_dir'         => '/tmp/OlcsBe/Proxy',

                // namespace for generated proxy classes
                'proxy_namespace'   => 'OlcsBe\Proxy',

                //'types' => array('yesno' => 'OlcsDb\Entity\Types\YesNoType')
            ),
        )
    ),
);
