<?php

if (! function_exists('key_array_of_entities_by')) {

    /**
     * @param array $entities
     * @param string $getterFunctionName
     * @return array
     */
    function key_array_of_entities_by(array $entities, $getterFunctionName = 'getId')
    {
        $keyed = [];

        foreach ($entities as $entity) {
            if (method_exists($entity, $getterFunctionName)) {
                $keyed[$entity->$getterFunctionName] = $entity;
            }
        }

        return $keyed;
    }

}
