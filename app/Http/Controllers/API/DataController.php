<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Course;
use Illuminate\Http\Request;

set_time_limit(0);
ini_set('memory_limit', '500M');

class DataController extends Controller
{
    const NUMBER_ROWS_IN_PROCESSING = 150000;

    
    /**
     * parsing data from a file and writing to DB
     *
     * @return void
     */
    public function getData()
    {      
        $collection = $this->fileProcessing()->sortBy(['id_send_currency', 'id_recive_currency'])->values();
        return $collection;
        $this->saveDB($collection);

        return response()->json('File downloaded sacsessfully');
    }
    
    /**
     * @param  Collection $collection
     * @return void
     */
    public function saveDB($collection)
    {
        foreach ($collection as $item) {
            $item['created_at'] = date("Y-m-d H:i:s");
            Course::create($item);
        }
    }
    
    /**
     * fileProcessing
     *
     * @param  Collection $collection
     */
    public function fileProcessing()
    {
        $collection = collect();
        $file = $_SERVER['DOCUMENT_ROOT'] . "\images\bm_rates.dat";
        $i = 0;
        $fd = fopen($file, 'r') or die("I don`t open file");
        while (!feof($fd)) {
            $i++;
            $str = htmlentities(fgets($fd));
            $this->addRowCollection($str, $collection);
            if ($i === self::NUMBER_ROWS_IN_PROCESSING) {
                $collection = $this->refreshCollection($collection);
                $i = 0;
            }
        }
        fclose($fd);
        $collection = $this->refreshCollection($collection);
        return $collection;
    }
    
    /**
     * refreshCollection
     *
     * @param  Collection $resultCollection
     * @return $resultCollection
     */
    public function refreshCollection($resultCollection)
    {
        $grouped = $resultCollection->groupBy(['id_send_currency', 'id_recive_currency']);
        unset($resultCollection);
        $resultCollection = collect();
        $this->getBigRate($grouped, $resultCollection);
        unset($grouped);
        return $resultCollection;
    }
    
    /**
     * getBigRate
     *
     * @param  Collection $grouped
     * @param  Collection $collection
     * @return void
     */
    public function getBigRate($grouped, &$collection)
    {
        foreach ($grouped as $item) {
            foreach ($item as $value) {
                if (count($value) !== 1) {
                    if ($value[0]['rate_send'] != 1) {
                        $min_value =  $value->min('rate_send');
                        $collection->push($value->where('rate_send', $min_value)->values()[0]);
                    } else {
                        $max_value = $value->max('rate_recive');
                        $collection->push($value->where('rate_recive', $max_value)->values()[0]);
                    }
                } else {
                    $collection->push($value[0]);
                }
            }
        }
    }
    
    /**
     * addRowCollection
     *
     * @param  string $str
     * @param  Collection $collection
     * @return void
     */
    public function addRowCollection($str, &$collection)
    {
        $keys = ['id_send_currency', 'id_recive_currency', 'id_exchange_office', 'rate_send', 'rate_recive'];
        $row = explode(";", $str);
        $value = array_slice($row, 0, 5);
        $arr = [];
        foreach ($value as $index => &$item) {
            $arr[$keys[$index]] = Str::contains($item, '.') ? (float)$item : (int)$item;
        }
        $collection->push($arr);
    }
}
