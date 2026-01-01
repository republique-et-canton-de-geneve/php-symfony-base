<?php

namespace EtatGeneve\ConfParameterBundle\Service;

use Dom\Entity;
use EtatGeneve\ConfParameterBundle\ConfParameter;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use  Symfony\Contracts\Cache\CacheInterface      ;
class ConfParameterManager
{
    private ConfParameter $confParameter;
    public function __construct(
        private string $EntityClassName,
        private ManagerRegistry $managerRegistry,
         private CacheInterface $cache
    ) {}


    public function init(ConfParameter $confParameter)    {
        $this->confParameter = clone $confParameter;
        $em = $this->managerRegistry->getManagerForClass($this->EntityClassName);
 try {

            // Initializes a 'ReflectionClass' class that allows you to report certain information from a class.
            // In this case, the 'Parameter' class.
            $reflectionClass = new ReflectionClass($confParameter);
            // Get all the properties of the class as an array of 'ReflectionProperty' objects.
        //     $this->paramProperties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
        //     $paramNames = [];
        //     // Get the 'name' value and the default values of each object present
        //     // in $paramProperties.
        //     foreach ($this->paramProperties as $paramProperty) {
        //         $paramName = $paramProperty->getName();
        //         // Add the 'name' to the final array.
        //         $paramNames[] = $paramName;
        //         // Adds to the array "$paramName" as key and the default text of the properties as value
        //         /* @phpstan-ignore-next-line */
        //         $this->defaultValues[$paramName] = $this->{$paramName};
        //     }

        //     /** @var ParameterEntity[] $dbParameters */
        //     $dbParameters = $this->cache->get(
        //         self::PARAMETER_KEY_CACHE,
        //         function (ItemInterface $item) use ($entityManager) {
        //             $item->expiresAfter(7200);
        //             // Selecting the 'parameter' table from the database, using the Doctrine QueryBuilder
        //             $mainQuery = $entityManager->createQueryBuilder();
        //             $mainQuery->select('parameter')
        //                 ->from(ConfParameter::class, 'parameter');

        //             return $mainQuery->getQuery()->getResult();
        //         },
        //         0.0
        //     );
        //     // Loop over the array containing all records (stored as objects) of the 'parameter' table
        //     foreach ($dbParameters as $dbParameter) {
        //         // Retrieves the 'name' value of the records
        //         $dbParameterName = $dbParameter->getName();
        //         // Checks if the 'name' field of the record matches that of one of the parameters
        //         if (in_array($dbParameterName, $paramNames)) {
        //             //  retrieve the 'value' of the record
        //             $dbParameterValue = $dbParameter->getValue();
        //             //  changes the value of the parameter in question to that of the database record
        //             // And adds the record to the '$dbParameterValue' table
        //             $this->{$dbParameterName} = $dbParameterValue;
        //             $this->currentValues[$dbParameterName] = $dbParameterValue;
        //         }
        //   }
        // } catch (Throwable) {
        //     throw new ExceptionApplication('Erreur fatal paramètre DB');
        // }
    } catch (\Throwable $e) {
            throw $e;
        }

    }
}
