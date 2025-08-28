<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Doctrine\ORM\EntityManagerInterface;
use DOMElement;
use Exception;
use FriendsOfBehat\SymfonyExtension\Driver\SymfonyDriver;
use Throwable;

class SymfonyContext extends BaseContext implements Context
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Reset the cookie ( php session).
     */
    protected function resetSessionCookie(): void
    {
        $session = $this->getSession();
        try {
            if ($session->isStarted()) {
                $session->setCookie('MOCKSESSID', null);
            }
        } catch (Throwable) {
            // to please sonar and cs !
        }
    }

    /**
     * Authenticate with a login and one or more roles.
     *
     * Example: Given I am login as "test" with role "APP.EDG.ADMIN"
     * Example: When I will be login as "test" with role "APP.EDG.UTILISATEUR"
     */
    #[Given('I am login as :login with role :roles')]
    #[When('I will be login as :login with role :roles')]
    public function loginAsWithRole(string $login, string $roles): void
    {
        $this->resetSessionCookie();
        $_ENV['APP_AUTH_FROM_ENV_VAR'] = true;
        $_ENV['APP_USER_LOGIN'] = $login;
        $_ENV['APP_USER_EMAIL'] = $login . '@ge.ch';
        $_ENV['APP_USER_FULLNAME'] = 'Behat ' . $login;
        $_ENV['APP_USER_ROLES'] = $roles;
    }

    /**
     * Set the server type
     * Example:
     *    Given server type is "prod"
     *    When server type is "dev".
     */
    #[When('server type is :value')]
    public function serverTypeIs(string $value): void
    {
        $_ENV['APP_SERVER_TYPE'] = $value;
    }

    /**
     * Commit data in db.
     * We use DAMA/DoctrineTestBundle, normaly no data are commit in the DB,
     * but we can force the commit.
     */
    #[Then('I commit db')]
    public function iCommitDb(): void
    {
        StaticDriver::commit();
        StaticDriver::beginTransaction();
    }

    /**
     * Exception simulation when a SQL is executed then match a regex pattern.
     *
     *  Example: Then I throw exception when sql match pattern "SELECT * FROM MYTABLE"
     */
    #[Then('I throw exception when sql match pattern :pattern')]
    public function IThrowExceptionWhenSqlMatchPattern(string $pattern): void
    {
        $GLOBALS['BEHAT_DOCTRINE_SQL_THROW_EXCEPTION'] = '/' . $pattern . '/i';
    }

    /**
     * Stop Exception simulation when a SQL is executed then match a regex pattern.
     *
     *  Example: Then I reset throw exception when sql match pattern
     */
    #[Then('I reset throw exception when sql match pattern')]
    public function IResetThrowExceptionWhenSqlMatchPattern(): void
    {
        $GLOBALS['BEHAT_DOCTRINE_SQL_THROW_EXCEPTION'] = null;
    }

    /**
     * When I get root dir
     * And I save it into "rootdir".
     *
     * @return array<int,string>
     */
    #[When('I read root dir')]
    public function iGetRootDir(): array
    {
        $rootDir = __DIR__ . '/../../..';
        $rootDir = realpath($rootDir);
        if (false === $rootDir) {
            throw new Exception('Root dir not found.');
        }

        return [$rootDir];
    }

    /**
     * For store a value
     * Example:
     *    Given a value "50"
     *    And I save it into "myvar"
     *    When I go to "/dg/<<myvar>>".
     */
    #[Given('a value :value')]
    public function aValue(string $value): string
    {
        return $value;
    }

    /**
     * For store 2 values
     * Example
     *   Given values "test" and "1"
     *   And I save it into "url,id"
     *   When I go to "/<<url>>/<<id>>".
     *
     * @return string[]
     */
    #[Given('values :value1 and :value2')]
    public function valuesAnd(string $value1, string $value2): array
    {
        return [$value1, $value2];
    }

    /**
     * Make a comparaison.
     *   Then <<x>> should equal "5".
     *
     * @throws Exception
     */
    #[Then(':a should equal :b')]
    public function shouldEqual(string $a, string $b): void
    {
        if ($a !== $b) {
            throw new Exception("a ($a) does not equal b ($b)");
        }
    }

    /**
     *  Execute a sql request
     *  When I execute sql query "UPDATE table SET column1 = 'value1', column2 ='value2'".
     */
    #[When('I execute sql query :sql')]
    public function executeSqlQuery(string $sql): void
    {
        try {
            $this->entityManager->getConnection()->executeQuery($sql);
        } catch (Throwable) {
        }
    }

    /**
     *  When I test
     *  When I execute sql query "UPDATE table SET column1 = 'value1', column2 ='value2'".
     */
    #[When('I test')]
    public function test(): void
    {
        printf('%s', 'test');
    }

    /**
     * Read a field for storing in var.
     *
     * When I read field 'comment'
     * And I save it into "myField"
     *
     * @return array<int,mixed>
     */
    #[When('I read field :name')]
    public function iReadField(string $name): array
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $node = $page->findField($name);
        if (!$node) {
            throw new Exception("Field '$name' not found.");
        }
        $value = $node->getValue();

        return [$value];
    }

    /**
     * Read an attribut on css element
     * Example: When I read "disabled" attribut on "#button:send" element.
     *          And I save it into "value".
     *
     * @return array<int,string>
     *
     * @throws Exception
     */
    #[Given('I read:name attribut on :element element')]
    public function iReadAttributOnElement(string $name, string $element): array
    {
        $session = $this->getSession();
        /** @var SymfonyDriver $driver */
        $driver = $session->getDriver();
        $page = $session->getPage();
        $client = $driver->getClient();
        $crawler = $client->getCrawler();
        $nodes = $page->findAll('css', $element);
        if (!$nodes) {
            throw new Exception("Element '$element' not found.");
        }
        $value = '';
        try {
            foreach ($nodes as $node) {
                /** @var DOMElement $element */
                $element = $crawler->filterXPath($node->getXpath())->getNode(0);
                $value = $element->getAttribute($name);
                break;
            }
        } catch (Throwable) {
            throw new Exception('Attribut not found');
        }

        return [$value];
    }
}
