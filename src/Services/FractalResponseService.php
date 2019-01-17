<?php

namespace Railroad\Doctrine\Services;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Illuminate\Http\JsonResponse;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\TransformerAbstract;
use Railroad\Doctrine\Routes\PaginationUrlGenerator;
use Spatie\Fractal\Fractal;

class FractalResponseService
{
    /**
     * @param $dataOrDatum
     * @param $type
     * @param TransformerAbstract $transformer
     * @param ArraySerializer $serializer
     * @param QueryBuilder|null $queryBuilder
     * @return Fractal
     */
    public static function create(
        $dataOrDatum,
        $type,
        TransformerAbstract $transformer,
        ArraySerializer $serializer,
        QueryBuilder $queryBuilder = null
    ) {
        $response = fractal(null, $transformer, $serializer);

        // if we pass the array of entities directly in to the fractal constructor the type doesnt get set, so we must
        // use ->collection or ->item to set the data for the response
        if (is_iterable($dataOrDatum)) {
            $response->collection($dataOrDatum, null, $type);
        } else {
            $response->item($dataOrDatum, null, $type);
        }

        if (!is_null($queryBuilder)) {
            $response->paginateWith(
                new DoctrinePaginatorAdapter(
                    new Paginator($queryBuilder), [PaginationUrlGenerator::class, 'generate']
                )
            );
        }

        return $response;
    }

    /**
     * @param int $statusCode
     * @param array $headers
     * @param int $options
     * @return JsonResponse
     */
    public static function empty($statusCode = 200, $headers = [], $options = 0)
    {
        $response = new JsonResponse();

        if (is_int($statusCode)) {
            $response->setStatusCode($statusCode);
        }

        if (is_array($headers)) {
            return $response->withHeaders($headers);
        }

        if (is_int($options)) {
            $response->setEncodingOptions($options);
        }

        if (is_callable($options)) {
            $options($response);
        }

        if (is_callable($statusCode)) {
            $statusCode($response);
        }

        if (is_callable($headers)) {
            $headers($response);
        }

        return $response;
    }
}