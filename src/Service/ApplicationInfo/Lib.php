<?php

namespace App\Service\ApplicationInfo;

use App\ExceptionApplication;
use App\Service\ApplicationInfo\Output\InterfaceOutput;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Throwable;

abstract class Lib
{
    public InterfaceOutput $output;
    public string $rootDirectoy;
    public string $logDirectory;
    public string $tmpDirectory;
    public string $tmpFileTest;
    /** @var string[] */
    public array $env;

    public function __construct(InterfaceOutput $output)
    {
        $this->output = $output;
        try {
            $this->rootDirectoy = realpath(dirname(__DIR__) . '/../..') ?: '';
            $this->loadEnvVar();
            $this->logDirectory = $this->env['APP_LOG_DIR'] ?? $this->rootDirectoy . '/var/log';
            $this->tmpDirectory = $this->env['APP_CACHE_DIR'] ?? $this->rootDirectoy . '/var/cache';
            $this->tmpFileTest = $this->tmpDirectory . '/tempFileTest.tmp';
            $this->output->top();
            $this->execute();
        } catch (Throwable $e) {
            $this->output->erreur($e->getMessage());
        }
        $this->output->bottom();
    }

    protected function loadEnvVar(): void
    {
        $env = [];
        if (file_exists($this->rootDirectoy . '/.env.local')) {
            /** @var string[]|false $parse */
            $parse = parse_ini_file($this->rootDirectoy . '/.env.local');
            if (false !== $parse) {
                $env += $parse;
            }
        }
        if (file_exists($this->rootDirectoy . '/.env')) {
            /** @var string[]|false $parse */
            $parse = parse_ini_file($this->rootDirectoy . '/.env');
            if (false !== $parse) {
                $env += $parse;
            }
        }

        /** @var string[] $_ENV */
        $env += $_ENV;
        $this->env = $env;
    }

    protected function delTree(string $dir, bool $root = true): void
    {
        if (is_dir($dir)) {
            $dirList = scandir($dir);
            if ($dirList) {
                $files = array_diff($dirList, ['.', '..']);
                foreach ($files as $file) {
                    (is_dir("$dir/$file")) ?
                        $this->delTree("$dir/$file", true) : unlink("$dir/$file");
                }
            }
            if ($root) {
                @rmdir($dir);
            }
        }
    }

    public function testVersion(): void
    {
        $this->output->title('Test de la version');
        try {
            $propertiesContent = parse_ini_file($this->rootDirectoy . '/release.properties');
            if (!isset($propertiesContent['version'])) {
                throw new ExceptionApplication('version manquante dans le fichier release.properties');
            }
            /** @var string $version */
            $version = $propertiesContent['version'];

            $this->output->line('Version : ' . $this->output->ok($this->output->filter($version)));
        } catch (Throwable $e) {
            $this->output->erreur($this->output->filter($e->getMessage()));
        }
    }

    /**
     * @param string[] $requiredextensions
     */
    public function testExtensions(array $requiredextensions): void
    {
        $this->output->title('Test les extensions du PHP');

        $loadedextensions = get_loaded_extensions();

        $diff = [];
        foreach ($requiredextensions as $extension) {
            if (!in_array($extension, $loadedextensions)) {
                $diff[] = $extension;
            }
        }
        if ([] !== $diff) {
            sort($requiredextensions);
            $this->output->line($this->output->ko('Il manque une ou plusieurs extensions:'));
            foreach ($diff as $module) {
                $this->output->line($this->output->ko($this->output->filter($module)));
            }
        } else {
            $this->output->line($this->output->ok("Toutes les extensions à l'application du php sont installées"));
        }
    }

    public function envReport(): void
    {
        $this->output->title('Informations');
        $phpcurrentver = substr(phpversion(), 0, 3);

        if ($phpcurrentver < '8.3') {
            $info = $this->output->ko($this->output->filter($phpcurrentver));
        } else {
            $info = $this->output->ok($this->output->filter($phpcurrentver));
        }
        $this->output->line('Version du PHP : ' . $info);

        $this->output->line('Valeur par defaut du umask : ' . $this->output->filter(decoct(umask())));
        umask(0);   // NOSONAR
        $this->output->line('Valeur du umask si mis à 0 : ' . $this->output->filter(decoct(umask())));

        $memSize = ini_get('memory_limit');
        $this->output->line('Taille maximale de la mémoire par défaut : ' . $this->output->filter($memSize));

        ini_set('memory_limit', '512M');
        $memNewSize = ini_get('memory_limit');
        $this->output->line('Modification de la taille maximale de la mémoire à 512M');
        if ('512M' == $memNewSize) {
            $info = $this->output->ok($this->output->filter($memNewSize));
        } else {
            $info = $this->output->ko($this->output->filter($memNewSize));
        }
        $this->output->line('Nouvelle taille maximale de la mémoire : ' . $info);
        $this->output->line('Modification de la taille maximale de la mémoire à 1024M');
        if ('1024M' == $memNewSize) {
            $info = $this->output->ok($this->output->filter($memNewSize));
        } else {
            $info = $this->output->ko($this->output->filter($memNewSize));
        }
        $this->output->line('Nouvelle taille maximale de la mémoire : ' . $info);

        $executionTime = ini_get('max_execution_time');
        $this->output->line('Valeur par défaut de max_execution_time : ' . $this->output->filter($executionTime));
        if (0 == $executionTime) {
            $this->output->line($this->output->ko('!!! infini timeout !!! ( peut etre en mode xdebug)'));
        } else {
            $this->output->line('Modification max_execution_time à 300 sec : ');
            $result = set_time_limit(300);
            if (!$result) {
                $this->output->line($this->output->ko("Il n'est pas possible de changer max_execution_time à 300 sec"));
            }
            $this->output->line('Nouvelle valeur de max_execution_time : ' . $this->output->filter($executionTime));
        }

        if ('' !== date_default_timezone_get() && '0' !== date_default_timezone_get()) {
            $timezone = $this->output->filter(date_default_timezone_get());
        } else {
            $timezone = $this->output->ko($this->output->filter('non définit'));
        }

        $this->output->line('Time zone : ' . $timezone);
        $this->output->line('Date locale ' . date('d.m.Y H:i:s') . ' décalage horaire : ' . date('P  e'));
        $this->output->line('Date GMT : ' . gmdate('d.m.Y H:i:s'));
    }

    public function resetCache(): void
    {
        $this->output->title('Reset des caches');

        $this->output->line('Reset de opcache');
        if (function_exists('opcache_reset')) {
            // reset du cache php
            $status = opcache_reset();
            if ($status) {
                $this->output->line($this->output->ok('Opcache effacé'));
            } else {
                $this->output->erreur('Reset de opcache : ' . $this->output->ko('KO'));
            }
        } else {
            $this->output->erreur("Le cache opcache n'est pas installé");
        }
        $this->output->line('Effacement du répertoire temporaire');
        try {
            $this->delTree(rtrim($this->tmpDirectory, '/'), false);
            $this->output->line($this->output->ok('Répertoire temporaire effacé'));
        } catch (Throwable $e) {
            $this->output->erreur($this->output->filter($e->getMessage()));
        }
    }

    public function testDirectory(): void
    {
        $this->output->title('Test écriture du répertoire temporaire de cache');
        $this->output->line('Test la présence du répertoire temporaire de cache');
        try {
            if (!realpath($this->tmpDirectory)) {
                $this->output->erreur("La configuration du répertoire temporaire de cache n'est pas définie");
            }
            if (!is_dir($this->tmpDirectory)) {
                $this->output->line('Création du du répertoire temporaire');
                mkdir($this->tmpDirectory, 0777, true);
            }
            if (is_file($this->tmpFileTest)) {
                unlink($this->tmpFileTest);
            }
            if (file_put_contents($this->tmpFileTest, 'TESTCONTENT')) {
                $this->output->line('Ecriture de fichier : ' . $this->output->ok('OK'));
                $filecontent = file_get_contents($this->tmpFileTest);
                if ('TESTCONTENT' == $filecontent) {
                    $this->output->line('Lecture de fichier : ' . $this->output->ok('OK'));
                    unlink($this->tmpFileTest);
                    if (!is_file($this->tmpFileTest)) {
                        $this->output->line('Suppression de fichier : ' . $this->output->ok('OK'));
                        $this->output->line('Répertoire temporaire de cache : ' . $this->output->ok('OK'));
                    } else {
                        $this->output->erreur(
                            'Impossible de supprimer un fichier dans le répertoire temporaire'
                        );
                    }
                } else {
                    $this->output->erreur('Impossible de lire un fichier dans le répertoire temporaire');
                }
            } else {
                $this->output->erreur("Impossible d'écrire un fichier dans le répertoire temporaire");
            }

            $this->output->line('Test la présence du répertoire des fichiers de log');
            if (!realpath($this->logDirectory)) {
                $this->output->erreur("La configuration du répertoire des fichiers de log n'est pas définie");
            }

            if (!is_dir($this->logDirectory)) {
                $this->output->line('Création du du répertoire des fichiers de log');
                mkdir($this->logDirectory, 0777, true);
            }
            $this->output->line('Répertoire des fichiers de log : ' . $this->output->ok('OK'));
        } catch (Throwable $e) {
            $this->output->erreur($this->output->filter($e->getMessage()));
        }
    }

    public function testDB(string $dsn, string $sql, string $dbname = ''): void
    {
        $this->output->title('Test de la DB ' . $this->output->filter($dbname));
        try {
            $this->output->line('Test de la connexion avec la DB');
            $dsn = str_replace('%kernel.project_dir%', $this->rootDirectoy, $dsn);
            $dsnParser = new DsnParser();
            $connectionParams = $dsnParser->parse($dsn);
            $conn = DriverManager::getConnection($connectionParams);
            $this->output->line('Connexion : ' . $this->output->ok('OK'));
            $stmt = $conn->executeQuery($sql);
            $this->output->line('Requete SQL ' . $this->output->ok('OK'));
            $stmt->fetchAssociative();
            $this->output->line('Lecture table ' . $this->output->ok('OK'));
        } catch (Throwable $e) {
            $this->output->erreur($this->output->filter($e->getMessage()));
        }
    }

    abstract public function execute(): void;
}
