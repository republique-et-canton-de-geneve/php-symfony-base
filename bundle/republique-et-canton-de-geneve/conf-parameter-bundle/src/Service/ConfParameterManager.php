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

    private ConfParameter $defaultConfParameter;
    private ConfParameter $alterConfParameter;
    private ?EntityManager $entityManager;

    private ReflectionClass $reflectionClass;

    private const KEY_CACHE = 'ConfParameterKey';

    public function __construct(
        private string $EntityClassName,
        private ManagerRegistry $managerRegistry,
        private CacheInterface $cache
    ) {
        /** @var EntityManager */
        $this->entityManager = $this->managerRegistry->getManagerForClass($this->EntityClassName);
    }

    public function init(ConfParameter $confParameter)
    {
        $this->alterConfParameter =  $confParameter;
        $this->defaultConfParameter = clone $confParameter;
        $this->entityManager = $this->managerRegistry->getManagerForClass($this->EntityClassName);
        $alterConfParameters = $this->getAlterConfParameters();
        $this->reflectionClass = new ReflectionClass($confParameter);
        foreach ($alterConfParameters as $alterConfParameter) {
            try {
                $this->reflectionClass->getProperty($alterConfParameter->getName())
                    ->setValue($confParameter, $alterConfParameter->getValue());
            } catch (ReflectionException $e) {
                $this->removeDbConfParameter($alterConfParameter->getName());
            }
        }
    }


    /**
     *
     * @return ConfParameterEntity[]
     */
    protected function getAlterDbConfParameters(): array
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

    public function setConfParameter(string $name, $value): void
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
        $this->cache->delete(self::KEY_CACHE);
    }

    /**
     * @return ConfParameterEntity[]
     */
    protected function getAlterConfParameters(): array
    {
        return $this->cache->get(
            self::KEY_CACHE,
            function (ItemInterface $item) {
                $item->expiresAfter(60);
                return $this->getAlterDbConfParameters();
            },
            0.0
        );
    }


    public function getConfParameters(): array
    {

        $reflectionClass = new ReflectionClass($this->defaultConfParameter);
        $confParameters = [];
        foreach ($this->reflectionClass->getProperties() as $property) {
            $name = $property->getName();
            $value = $property->getValue($this->alterConfParameter);
            $defValue = $property->getValue($this->defaultConfParameter);
            $confParameters[$name] = ['name' => $name, 'value' => $value, 'default' => $defValue];
        }
        return $confParameters;
    }
}
