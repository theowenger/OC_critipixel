<?php

namespace App\Tests;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\KernelInterface;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use App\Kernel; // Assure-toi que le chemin correspond à ton projet

class ReloadFixturesListener implements TestListener
{
    use TestListenerDefaultImplementation;

    private KernelInterface $kernel;

    public function __construct(string $env = 'test', bool $debug = false)
    {
        $this->kernel = new Kernel($env, $debug); // Instanciation correcte du kernel
    }

    public function startTestSuite(\PHPUnit\Framework\TestSuite $suite): void
    {
        $this->kernel->boot();
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:fixtures:load',
            '--no-interaction' => true,
            '--purge-with-truncate' => true,
        ]);

        $output = new NullOutput();
        $application->run($input, $output);

        // Ensuite, arrêter le kernel après l'exécution de la commande
        $this->kernel->shutdown();
    }
}