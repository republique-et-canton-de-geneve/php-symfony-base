<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\BaseParameter;
use App\Entity\Parameter as ParameterEntity;
use App\Kernel;
use App\Service\ApplicationInfo\Output\PhpOutput;
use App\Service\ApplicationInfo\Output\SondeOutput;
use App\Service\ApplicationInfo\Output\SymfonyOutput;
use App\Service\ApplicationInfo\Php;
use App\Service\ApplicationInfo\SondePhp;
use App\Service\ApplicationInfo\Symfony;
use Behat\Behat\Context\Context;
use Behat\Step\When;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class AppContext extends SymfonyContext implements Context
{
    protected Kernel $kernel;

    public function __construct(EntityManagerInterface $entityManager, Kernel $kernel)
    {
        parent::__construct($entityManager);
        $this->kernel = $kernel;
    }

    /**
     *  Set a value to a app parameter.
     *
     * Example: When I set parameter "modeMaintenance" to "1"
     */
    #[When('I set parameter :name to :value')]
    public function iSetParameter(string $name, string $value): void
    {
        /** @var ParameterEntity[] $parameters */
        $parameters = $this->entityManager->getRepository(ParameterEntity::class)->findBy(['name' => $name]);
        if ($parameters) {
            $parameter = $parameters[0];
        } else {
            $parameter = new ParameterEntity();
            $parameter->setName($name);
            $this->entityManager->persist($parameter);
        }
        $parameter->setValue($value);
        $this->entityManager->flush();
        $cache = new FilesystemAdapter();
        $cache->delete(BaseParameter::PARAMETER_KEY_CACHE);
    }

    #[When('I test applicationInfo service')]
    public function iTestSondeService(): void
    {
        $sondeOutput = new SondeOutput();
        $phpOutput = new PhpOutput();
        $symfonyOutput = new SymfonyOutput();

        // test SondePhp and output class
        ob_start();
        $app1 = new SondePhp($sondeOutput);
        unset($app1);
        $app1 = new SondePhp($phpOutput);
        ob_end_clean();
        unset($app1);
        $app1 = new SondePhp($symfonyOutput);
        unset($app1);

        // test Symfony class
        $app1 = new Symfony($symfonyOutput);
        unset($app1);

        // test Php class
        // we can't erase symfony cache directory, the system crach. We use a 'mock' path
        $rootDirectory = realpath(dirname(__DIR__) . '/..') ?: '';
        $oldDir = $_ENV['APP_CACHE_DIR'] ?? null;
        $newDir = $rootDirectory . '/var/cache/behat_test';
        mkdir($newDir, 0777, true);
        $_ENV['APP_CACHE_DIR'] = $newDir;
        $app1 = new Php($symfonyOutput);
        unset($app1);
        $_ENV['APP_CACHE_DIR'] = $oldDir;
        @rmdir($newDir);
    }
}
