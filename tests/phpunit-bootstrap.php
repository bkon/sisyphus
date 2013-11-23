<?php

function doAutoloadSrc($className)
{
    $pathParts = explode('_', $className);
    $filename = join(DIRECTORY_SEPARATOR, $pathParts) . '.php';
    $base = realpath(
        join(
            DIRECTORY_SEPARATOR,
            array(
                dirname(__FILE__),
                '..',
                'src',
                $filename
            )
        )
    );

    if (file_exists($base)) {
        require_once($base);
    };
}

function doAutoloadTest($className)
{
    $pathParts = explode('_', $className);
    $filename = join(DIRECTORY_SEPARATOR, $pathParts) . '.php';
    $base = realpath(
        join(
            DIRECTORY_SEPARATOR,
            array(
                dirname(__FILE__),
                $filename
            )
        )
    );

    if (file_exists($base)) {
        require_once($base);
    };
}

spl_autoload_register('doAutoloadSrc');
spl_autoload_register('doAutoloadTest');