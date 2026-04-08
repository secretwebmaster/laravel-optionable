<?php

$autoloadPaths = [
    __DIR__.'/../vendor/autoload.php',
    __DIR__.'/../../../../vendor/autoload.php',
];

foreach ($autoloadPaths as $autoloadPath) {
    if (file_exists($autoloadPath)) {
        require $autoloadPath;

        foreach ([
            __DIR__.'/TestCase.php',
            __DIR__.'/Models/TestPage.php',
        ] as $supportFile) {
            require_once $supportFile;
        }

        return;
    }
}

fwrite(STDERR, "Unable to locate Composer autoload.php for test bootstrap.\n");
exit(1);
