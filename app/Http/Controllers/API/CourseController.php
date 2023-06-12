<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function getTwoCourses($send_currency, $recive_currency)
    {
        return Course::where('id_send_currency', $send_currency)
            ->where('id_recive_currency', $recive_currency)->get();
    }

    public function getCourses()
    {
        $send_currencies = [];
        if (isset($_GET["send_currency"])) {
            $send_currencies = $_GET["send_currency"];
        }
        $recive_currencies = [];
        if (isset($_GET["recive_currency"])) {
            $recive_currencies = $_GET["recive_currency"];
        }
        if (!(count($send_currencies) > 0 || count($recive_currencies) > 0)) {
            return Course::get();
        }

        if (count($send_currencies) > 0) {
            $courses = Course::whereIn('id_send_currency', $send_currencies);
        }
        if (count($recive_currencies) > 0) {

            if (!isset($courses)) {
                $courses = Course::whereIn('id_recive_currency', $recive_currencies);
            } else {
                $courses->orWhereIn('id_recive_currency', $recive_currencies);
            }
        }
        return $courses->get();
    }
}
