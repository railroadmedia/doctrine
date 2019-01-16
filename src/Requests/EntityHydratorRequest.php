<?php

namespace Railroad\Doctrine\Requests;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Throwable;

class EntityHydratorRequest extends FormRequest
{
    /**
     * Checks entityData keys
     * adds camelCase keys if snake or kebab cases are found
     *
     * @param array $entityData
     *
     * @return array
     */
    public static function camelize(array $entityData): array
    {
        foreach ($entityData as $key => $value) {
            if (strpos($key, '_') !== false || strpos($key, '-') !== false) {
                $normalizedKey = camel_case($key);
                $entityData[$normalizedKey] = $value;
            }
        }

        return $entityData;
    }

    /**
     * Creates an instance of the specified class
     *
     * @param string $className
     *
     * @return mixed
     *
     * @throws Throwable
     */
    public static function instantiate(string $className)
    {
        return new $className;
    }

    /**
     * Populates an entity with current request data
     *
     * @param string|object $target - entity
     * @param bool $normalize
     *
     * @return mixed
     */
    public function fromRequest($target, bool $normalize = true) {

        return $this->fromArray($target, $this->all(), $normalize);
    }

    /**
     * Populates an entity with data from an array
     *
     * @param string|object $target
     * @param array $entityData
     * @param bool $normalize
     *
     * @return mixed
     *
     * @throws Throwable
     */
    public function fromArray(
        $target,
        array $entityData,
        bool $normalize = true
    ) {
        $entityManager = $this->container->make(EntityManager::class);

        $normalizedEntityData = $normalize ?
            self::camelize($entityData) : $entityData;

        $targetObject = is_object($target) ?
            $target : self::instantiate($target);

        $targetClass = get_class($targetObject);

        /**
         * @var $classMeta \Doctrine\ORM\Mapping\ClassMetadata
         */
        $classMeta = $entityManager->getClassMetadata($targetClass);

        // handle basic entity mapping
        /**
         * @var $fieldsMapping array
         */
        $fieldsMapping = $classMeta->getFieldNames();

        foreach ($fieldsMapping as $property) {
            if (isset($normalizedEntityData[$property])) {

                $setter = 'set' . ucfirst($property);

                if (method_exists($targetObject, $setter)) {

                    $value = $normalizedEntityData[$property];

                    if (
                        $value
                        && $classMeta->getTypeOfField($property) == 'datetime'
                        && !is_object($value) // prop value already parsed
                    ) {
                        // entities expect \DateTimeInterface objects values
                        $value = Carbon::parse($value);
                    }

                    $targetObject->$setter($value);
                }
            }
        }

        // handle association mapping, without issuing db queries for related entities
        /**
         * @var $associations array
         */
        $associations = $classMeta->getAssociationNames();

        foreach ($associations as $assocName) {

            $setter = 'set' . ucfirst($assocName);

            if (
                method_exists($targetObject, $setter)
                && (isset($normalizedEntityData[$assocName])
                    || isset($normalizedEntityData[$assocName . 'Id']))
            ) {

                // searches for 'property' or 'propertyId'
                // includes transformed request keys 'property_id' or 'property-id'
                $associationId = $normalizedEntityData[$assocName] ??
                                $normalizedEntityData[$assocName . 'Id'];

                if ($associationId) {

                    $associatedEntity = $classMeta
                                    ->getAssociationTargetClass($assocName);

                    // this creates a doctrine proxy entity
                    // avoids querying the database for the related entity
                    $reference = $entityManager->getReference(
                        $associatedEntity,
                        $associationId
                    );

                    $targetObject->$setter($reference);

                } else {

                    // explicitly set relation as null
                    // useful for updating an existing entity
                    $targetObject->$setter(null);
                }
            }
        }

        return $targetObject;
    }
}
