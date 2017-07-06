<?php namespace App\Libraries;

class ArrayLib
{
    /**
     * Sort array data by Key
     *
     * @param array  $data
     * @param string $sortKey
     * @param const  $sort_flags
     *
     * @return array
     */
    public static function SortByKeyValue($data, $sortKey, $sort_flags = SORT_ASC)
    {
        if (empty($data) or empty($sortKey)) return $data;

        if (!isset($data[0][$sortKey])) return $data;

        $sortArray = array();

        foreach ($data as $item) {
            foreach ($item as $key => $value) {
                if (!isset($sortArray[$key])) {
                    $sortArray[$key] = array();
                }
                $sortArray[$key][] = $value;
            }
        }

        array_multisort($sortArray[$sortKey], $sort_flags, $data);

        return array_values($data);
    }

}