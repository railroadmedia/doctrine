<?php

namespace Railroad\Doctrine\Serializers;

use Carbon\Carbon;
use DateTime;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\ClassMetadata;

class BasicEntitySerializer
{
    /**
     * @param $entity
     * @param ClassMetadata $classMetadata
     * @return array
     */
    public function serializeToUnderScores($entity, ClassMetadata $classMetadata)
    {
        $dataArray = $this->serialize($entity, $classMetadata);
        $formattedArray = [];

        foreach ($dataArray as $fieldName => $value) {

            if (mb_check_encoding($value) == false) {
                $value = utf8_encode($value);
            }

            $formattedArray[Inflector::tableize($fieldName)] = $value;
        }

        return $formattedArray;
    }

    /**
     * @param $entity
     * @param ClassMetadata $classMetadata
     * @return array
     */
    public function serialize($entity, ClassMetadata $classMetadata)
    {
        $dataArray = [];

        foreach ($classMetadata->getFieldNames() as $fieldName) {
            if (!$classMetadata->hasField($fieldName)) {
                continue;
            }

            $fieldType = $classMetadata->getTypeOfField($fieldName);
            $fieldValue = $classMetadata->getFieldValue($entity, $fieldName);

            switch ($fieldType) {
                case 'boolean':
                case 'decimal':
                case 'smallint':
                case 'integer':
                case 'bigint':
                case 'float':
                case 'string':
                case 'text':
                    $dataArray[$fieldName] = $fieldValue;
                    break;
                case 'datetime':
                case 'datetimetz':
                    if ($fieldValue instanceof Carbon) {
                        $dataArray[$fieldName] = $fieldValue->toDateTimeString();
                    } elseif ($fieldValue instanceof DateTime) {
                        $dataArray[$fieldName] = $fieldValue->format("Y-m-d H:i:s");
                    } else {
                        $dataArray[$fieldName] = null;
                    }

                    break;
                case 'date':
                    if ($fieldValue instanceof Carbon) {
                        $dataArray[$fieldName] = $fieldValue->toDateString();
                    } elseif ($fieldValue instanceof DateTime) {
                        $dataArray[$fieldName] = $fieldValue->format("Y-m-d");
                    } else {
                        $dataArray[$fieldName] = null;
                    }

                    break;
                case 'time':
                    $dataArray[$fieldName] = $fieldValue instanceof Carbon ? $fieldValue->timestamp : $fieldValue;

                    break;
                case 'object':
                case 'array':
                    $dataArray[$fieldName] = serialize($fieldValue);
                    break;
                case 'simple_array':
                    $dataArray[$fieldName] = implode(',', $fieldValue);
                    break;
                case 'json_array':
                    $dataArray[$fieldName] = json_encode($fieldValue);
                    break;
                default:
                    $dataArray[$fieldName] = (string)$fieldValue;
            }
        }

        return $dataArray;
    }
}