<?php

namespace EtatGeneve\ConfParameterBundle\Service;

use Doctrine\ORM\EntityManager;
use EtatGeneve\ConfParameterBundle\ConfParameter;
use EtatGeneve\ConfParameterBundle\Entity\ConfParameterEntity;
use ReflectionClass;
use ReflectionException;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use  Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ConfParameterManager
{

    private ConfParameter $confParameter;
    private EntityManager $entityManager;

    public function __construct(
        private string $EntityClassName,
        private ManagerRegistry $managerRegistry,
        private CacheInterface $cache
    ) {
        $this->entityManager = $this->managerRegistry->getManagerForClass($this->EntityClassName);
    }


    /**
     * Summary of getDbConfParameter
     * @return ConfParameterEntity[]
     */
    public function getAlterDbConfParameters(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from($this->EntityClassName, 'p')
            ->getQuery()
            ->getResult();
    }


    public function removeDbConfParameter(string $name): void
    {
        $confParameterEntity = $this->entityManager
            ->createQueryBuilder()
            ->delete('p')
            ->from($this->EntityClassName, 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->execute();
        $this->clearCache();
    }

    public function setConfParameter(string $name, $value):void
    {
        $dbConfParam = $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from($this->EntityClassName, 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getSingleResult();
        if (!$dbConfParam) {
            $dbConfParam = new $this->EntityClassName();
            $dbConfParam->setName($name);
            $this->entityManager->persist($dbConfParam);
        }
        $dbConfParam->setValue($value);
        $this->entityManager->flush();
        $this->clearCache();
    }

    public function clearCache(): void
    {
        $this->cache->delete($this->EntityClassName);
    }

    /**
     * @return ConfParameterEntity[]
     */
    public function getAlterConfParameters(): array
    {
        return $this->cache->get(
            $this->EntityClassName,
            function (ItemInterface $item) {
                $item->expiresAfter(60);
                return $this->getAlterDbConfParameters();
            },
            0.0
        );
    }
    public function init(ConfParameter $confParameter)
    {
        $this->confParameter = clone $confParameter;
        $em = $this->managerRegistry->getManagerForClass($this->EntityClassName);

        $alterConfParameters = $this->getAlterConfParameters();
        $reflectionClass = new ReflectionClass($confParameter);
        foreach ($alterConfParameters as $alterConfParameter) {
            try {
                $reflectionClass->getProperty($alterConfParameter->getName())
                    ->setValue($this->confParameter, $alterConfParameter->getValue());
            } catch (ReflectionException $e) {
                $this->removeDbConfParameter($alterConfParameter->getName());
            }
        }
    }
}
