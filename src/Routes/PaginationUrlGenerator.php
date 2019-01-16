<?php

namespace Railroad\Doctrine\Routes;

class PaginationUrlGenerator
{
    public static function generate($page)
    {
        return request()->fullUrlWithQuery(['page' => $page]);
    }
}