<?php namespace App\Libraries;

use Illuminate\Pagination\LengthAwarePaginator;

// TODO Refactor this class

class Pagination
{
    protected $paginator;


    public static function make($items, $perPage, $currentPage = null, array $options = [])
    {
        $total = count($items);
        $items = array_slice($items, ($currentPage * $perPage) - $perPage, $perPage);
        $paginator = new LengthAwarePaginator($items, $total, $perPage, $currentPage, $options);

        if (isset($options['url'])) {
            $paginator->setPath($options['url']);
        }


        return $paginator;
    }
}

