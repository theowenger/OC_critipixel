<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

// Exécutez le script shell pour charger les fixtures
$output = [];
$returnVar = 0;

exec('bash load_test_fixtures.sh', $output, $returnVar);

if ($returnVar !== 0) {
    echo "Erreur lors de l'exécution du script load_test_fixtures.sh : " . implode("\n", $output);
    exit($returnVar);
}