<?php

declare(strict_types=1);

return [
    'commands' => [],
    'configDir' => __DIR__ . '/build/psysh/config',
    'dataDir' => __DIR__ . '/build/psysh/data',
    'runtimeDir' => __DIR__ . '/build/psysh/tmp',
    'defaultIncludes' => [],
    'eraseDuplicates' => true,
    'errorLoggingLevel' => \E_ALL,
    'forceArrayIndexes' => false,
    'historySize' => 1000,
    'updateCheck' => 'never',
    'useBracketedPaste' => true,
    'verbosity' => \Psy\Configuration::VERBOSITY_NORMAL,
];