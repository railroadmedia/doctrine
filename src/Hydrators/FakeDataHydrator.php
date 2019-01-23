<?php

namespace Railroad\Doctrine\Hydrators;

use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Faker\Factory;
use Faker\Generator;
use League\Fractal\TransformerAbstract;

class FakeDataHydrator
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Generator
     */
    protected $faker;

    /**
     * FakeDataHydrator constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->faker = Factory::create();
    }

    /**
     * @param $entity
     * @param array $customColumnFormatters
     * @return mixed
     */
    public function fill(&$entity, $customColumnFormatters = [])
    {
        if (!is_object($entity)) {
            $entity = new $entity;
        }

        /**
         * @var $customColumnFormatters Closure[]
         */
        $customColumnFormatters = array_merge(
            $this->guessColumnFormatters($this->entityManager->getClassMetadata(get_class($entity)), $this->faker),
            $customColumnFormatters
        );

        foreach ($customColumnFormatters as $field => $format) {
            if (!is_null($format)) {

                $value = is_callable($format) ? $format([], $entity) : $format;

                $setter = sprintf("set%s", ucfirst($field));

                $entity->$setter($value);
            }
        }

        return $entity;
    }

    /**
     * @param $entity
     * @param TransformerAbstract $transformerAbstract
     * @param array $customColumnFormatters
     * @return mixed
     */
    public function getAttributeArray($entity, TransformerAbstract $transformerAbstract, $customColumnFormatters = [])
    {
        if (!is_object($entity)) {
            $entity = new $entity;
            $this->fill($entity, $customColumnFormatters);
        }

        $arrayData = $transformerAbstract->transform($entity);

        return $arrayData;
    }

    /**
     * @param ClassMetadata $classMetaData
     * @param \Faker\Generator $generator
     * @return array
     */
    public function guessColumnFormatters($classMetaData, \Faker\Generator $generator)
    {
        $formatters = [];
        $nameGuesser = new \Faker\Guesser\Name($generator);
        $columnTypeGuesser = new ColumnTypeGuesserCarbon($generator);

        foreach ($classMetaData->getFieldNames() as $fieldName) {
            if ($classMetaData->isIdentifier($fieldName) || !$classMetaData->hasField($fieldName)) {
                continue;
            }

            $size =
                isset($classMetaData->fieldMappings[$fieldName]['length']) ?
                    $classMetaData->fieldMappings[$fieldName]['length'] : null;

            if ($formatter = $nameGuesser->guessFormat($fieldName, $size)) {
                $formatters[$fieldName] = $formatter;
                continue;
            }
            if ($formatter = $columnTypeGuesser->guessFormat($fieldName, $classMetaData)) {
                $formatters[$fieldName] = $formatter;
                continue;
            }
        }

        foreach ($classMetaData->getAssociationNames() as $assocName) {
            if ($classMetaData->isCollectionValuedAssociation($assocName)) {
                continue;
            }

            $relatedClass = $classMetaData->getAssociationTargetClass($assocName);

            $unique = $optional = false;

            if ($classMetaData instanceof ClassMetadata) {
                $mappings = $classMetaData->getAssociationMappings();
                foreach ($mappings as $mapping) {
                    if ($mapping['targetEntity'] == $relatedClass) {
                        if ($mapping['type'] == ClassMetadata::ONE_TO_ONE) {
                            $unique = true;
                            $optional =
                                isset($mapping['joinColumns'][0]['nullable']) ? $mapping['joinColumns'][0]['nullable'] :
                                    false;
                            break;
                        }
                    }
                }
            }

            $index = 0;
            $formatters[$assocName] = function ($inserted) use ($relatedClass, &$index, $unique, $optional) {

                if (isset($inserted[$relatedClass])) {
                    if ($unique) {
                        $related = null;
                        if (isset($inserted[$relatedClass][$index]) || !$optional) {
                            $related = $inserted[$relatedClass][$index];
                        }

                        $index++;

                        return $related;
                    }

                    return $inserted[$relatedClass][mt_rand(0, count($inserted[$relatedClass]) - 1)];
                }

                return null;
            };
        }

        return $formatters;
    }
}