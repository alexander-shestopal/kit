<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

set_time_limit(0);
ini_set('memory_limit', '500M');

class FileController extends Controller
{
    const PROXY = '58.219.93.250:7890';
    // const PROXY = '222.212.148.225:7890';
    const URL_FILE_ZIP = 'http://api.bestchange.ru/info.zip';
    const FOLDER_AND_NAME_ZIP_FILE =  '\images\info.zip';
    const DATA_RATES = 'bm_rates.dat';
    
    /**
     * receiving and writing a file
     *
     * @return void
     */
    public function getFile()
    {
        $zipFile = $_SERVER['DOCUMENT_ROOT'] . self::FOLDER_AND_NAME_ZIP_FILE;
        $proxy = $this->getProxy();
        $download = $this->dowloadFileProxy(self::URL_FILE_ZIP, $zipFile, $proxy);
        if (!is_bool($download)) {
            return $download;
        }
        $this->extractZipFile($zipFile, self::DATA_RATES);

        return response()->json('File downloaded sacsessfully');
    }
    
    /**
     * @param  string $url
     * @param  string $zipFile
     * @param  string $proxy
     * @return void
     */
    public function dowloadFileProxy($url, $zipFile, $proxy)
    {
        $zipResource = fopen($zipFile, "w");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FILE, $zipResource);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $page = curl_exec($ch);
        curl_close($ch);
        if (!$page) {
            return "Error :-: " . curl_error($ch);
        }
        curl_close($ch);
        fclose($zipResource);
        return true;
    }

    public function getProxy()
    {
        return self::PROXY;
    }
    
    /**
     * @param  string $fileOpen
     * @param  string $file
     * @return void
     */
    public function extractZipFile($fileOpen, $file)
    {
        $zip = new ZipArchive();
        $zip->open($fileOpen);
        $path = $_SERVER['DOCUMENT_ROOT'] . '/images/';
        $zip->extractTo($path, $file);
        $zip->close();

        return true;
    }
}
