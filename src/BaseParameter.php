<?php

namespace App;

use App\Entity\Parameter as ParameterEntity;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Throwable;

class BaseParameter
{
    public const string PARAMETER_KEY_CACHE = 'parameter';
    /**
     * @var string[]
     */
    protected array $defaultValues = [];
    /**
     * @var array<string|null>
     */
    protected array $currentValues = [];
    protected FilesystemAdapter $cache;
    /**
     * @var ReflectionProperty[]
     */
    private array $paramProperties;
    /**
     * @var ?Param[]
     */
    private ?array $annotations = null;

    /**
     * @throws ExceptionApplication
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        try {
            $this->cache = new FilesystemAdapter();
            // Initializes a 'ReflectionClass' class that allows you to report certain information from a class.
            // In this case, the 'Parameter' class.
            $paramClass = new ReflectionClass($this);
            // Get all the properties of the class as an array of 'ReflectionProperty' objects.
            $this->paramProperties = $paramClass->getProperties(ReflectionProperty::IS_PUBLIC);
            $paramNames = [];
            // Get the 'name' value and the default values of each object present
            // in $paramProperties.
            foreach ($this->paramProperties as $paramProperty) {
                $paramName = $paramProperty->getName();
                // Add the 'name' to the final array.
                $paramNames[] = $paramName;
                // Adds to the array "$paramName" as key and the default text of the properties as value
                /* @phpstan-ignore-next-line */
                $this->defaultValues[$paramName] = $this->{$paramName};
            }

            /** @var ParameterEntity[] $dbParameters */
            $dbParameters = $this->cache->get(
                self::PARAMETER_KEY_CACHE,
                function (ItemInterface $item) use ($entityManager) {
                    $item->expiresAfter(7200);
                    // Selecting the 'parameter' table from the database, using the Doctrine QueryBuilder
                    $mainQuery = $entityManager->createQueryBuilder();
                    $mainQuery->select('parameter')
                        ->from(ParameterEntity::class, 'parameter');

                    return $mainQuery->getQuery()->getResult();
                },
                0.0
            );
            // Loop over the array containing all records (stored as objects) of the 'parameter' table
            foreach ($dbParameters as $dbParameter) {
                // Retrieves the 'name' value of the records
                $dbParameterName = $dbParameter->getName();
                // Checks if the 'name' field of the record matches that of one of the parameters
                if (in_array($dbParameterName, $paramNames)) {
                    //  retrieve the 'value' of the record
                    $dbParameterValue = $dbParameter->getValue();
                    //  changes the value of the parameter in question to that of the database record
                    // And adds the record to the '$dbParameterValue' table
                    $this->{$dbParameterName} = $dbParameterValue;
                    $this->currentValues[$dbParameterName] = $dbParameterValue;
                }
            }
        } catch (Throwable) {
            throw new ExceptionApplication('Erreur fatal paramètre DB');
        }
    }

    /**
     * @return string[]
     */
    public function getDefaultValues(): array
    {
        return $this->defaultValues;
    }

    /**
     * @return array<string|null>
     */
    public function getCurrentValues(): array
    {
        return $this->currentValues;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function clearCache(): void
    {
        $this->cache->delete(self::PARAMETER_KEY_CACHE);
    }

    /**
     * @return Param[]
     */
    public function getAnnotations(): array
    {
        if (null === $this->annotations) {
            $this->annotations = [];
            foreach ($this->paramProperties as $paramProperty) {
                $annotations = $paramProperty->getAttributes(Param::class);
                if ($annotations) {
                    $this->annotations[$paramProperty->getName()] = $annotations[0]->newInstance();
                }
            }
        }

        return $this->annotations;
    }
}
