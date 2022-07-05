<?php
namespace App\Controller\Etkinlik;

use DateTime;
use DateTimeZone;



class Event {

    // Tests whether the given ISO8601 string has a time-of-day or not
    const ALL_DAY_REGEX = '/^\d{4}-\d\d-\d\d$/'; // matches strings like "2013-12-29"

    public $title;
    public $allDay; // a boolean
    public $start; // a DateTime
    public $end; // a DateTime, or null
    public $properties = array(); // an array of other misc properties
    public $dataid;


    // Constructs an Event object from the given array of key=>values.
    // You can optionally force the timeZone of the parsed dates.
    public function __construct($array, $timeZone=null) {

        $this->title = $array['title'];

        $this->dataid = $array['id'];

        if (isset($array['allDay'])) {
            // allDay has been explicitly specified
            $this->allDay = (bool)$array['allDay'];
        }
        else {
            // Guess allDay based off of ISO8601 date strings
            $this->allDay = preg_match(self::ALL_DAY_REGEX, $array['start']) &&
                (!isset($array['end']) || preg_match(self::ALL_DAY_REGEX, $array['end']));
        }

        if ($this->allDay) {
            // If dates are allDay, we want to parse them in UTC to avoid DST issues.
            $timeZone = null;
        }

        // Parse dates
        $this->start = parseDateTime($array['start'], $timeZone);
        $this->end = isset($array['end']) ? parseDateTime($array['end'], $timeZone) : null;

        // Record misc properties
        foreach ($array as $name => $value) {
            if (!in_array($name, array('title', 'allDay', 'start', 'end'))) {
                $this->properties[$name] = $value;
            }
        }
    }


    // Returns whether the date range of our event intersects with the given all-day range.
    // $rangeStart and $rangeEnd are assumed to be dates in UTC with 00:00:00 time.
    public function isWithinDayRange($rangeStart, $rangeEnd) {

        // Normalize our event's dates for comparison with the all-day range.
        $eventStart = stripTime($this->start);

        if (isset($this->end)) {
            $eventEnd = stripTime($this->end); // normalize
        }
        else {
            $eventEnd = $eventStart; // consider this a zero-duration event
        }

        // Check if the two whole-day ranges intersect.
        return $eventStart < $rangeEnd && $eventEnd >= $rangeStart;
    }


    // Converts this Event object back to a plain data array, to be used for generating JSON
    public function toArray() {

        // Start with the misc properties (don't worry, PHP won't affect the original array)
        $array = $this->properties;

        $array['id'] = $this->dataid;

        $array['title'] = $this->title;

        // Figure out the date format. This essentially encodes allDay into the date string.
        if ($this->allDay) {
            $format = 'Y-m-d'; // output like "2013-12-29"
        }
        else {
            $format = 'c'; // full ISO8601 output, like "2013-12-29T09:00:00+08:00"
        }

        // Serialize dates into strings
        $array['start'] = $this->start->format($format);
        if (isset($this->end)) {
            $array['end'] = $this->end->format($format);
        }

        return $array;
    }

}


// Date Utilities
//----------------------------------------------------------------------------------------------


// Parses a string into a DateTime object, optionally forced into the given timeZone.
function parseDateTime($string, $timeZone=null) {
    $date = new DateTime(
        $string,
        $timeZone ? $timeZone : new DateTimeZone('UTC')
    // Used only when the string is ambiguous.
    // Ignored if string has a timeZone offset in it.
    );
    if ($timeZone) {
        // If our timeZone was ignored above, force it.
        $date->setTimezone($timeZone);
    }
    return $date;
}


// Takes the year/month/date values of the given DateTime and converts them to a new DateTime,
// but in UTC.
function stripTime($datetime) {
    return new DateTime($datetime->format('Y-m-d'));
}


class etkinlikViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    public function takvim() {

        return $this->view("etkinlik/takvim");
    }

    public function takvimEtkinlikIptal() {


        $model = $this->model("etkinlik","etkinlikModel");

        $remove = $model->takvimeEtkinlikIptal(self::$http_request);

        if($remove){
            echo json_encode([
                "status"=>1
            ]);
        }else{
            echo json_encode([
                "status"=>2
            ]);
        }



    }


    public function takvimEtkinlikGuncelle() {


        $model = $this->model("etkinlik","etkinlikModel");
        $update = false;

        if( self::$http_request->input("updatetype") == "full"){
            $update = $model->takvimeEtkinlikGuncelle(self::$http_request);

        }else  if( self::$http_request->input("updatetype") == "title"){
            $update = $model->takvimeEtkinlikTitleGuncelle(self::$http_request);
        }


        if($update){
            echo json_encode([
                "status"=>1
            ]);
        }else{
            echo json_encode([
                "status"=>2
            ]);
        }


    }


    public function takvimEtkinlikEkle() {


        $model = $this->model("etkinlik","etkinlikModel");
        $insert = $model->takvimeEtkinlikEkle(self::$http_request);

        if($insert){
            echo json_encode([
                "status"=>1
            ]);
        }else{
            echo json_encode([
                "status"=>2
            ]);
        }


    }

    public function etkinlikAl() {




        $range_start = parseDateTime($_GET['start']);
        $range_end = parseDateTime($_GET['end']);

        $time_zone = null;
        if (isset($_GET['timeZone'])) {
            $time_zone = new DateTimeZone($_GET['timeZone']);
        }


        $model = $this->model("etkinlik","etkinlikModel");
        $input_arrays = $model->etkinlikleriAl($_GET['start'],$_GET['end']);
        $output_arrays = array();
        foreach ($input_arrays as $array) {

            $event = new Event($array, $time_zone);

            if ($event->isWithinDayRange($range_start, $range_end)) {
                $output_arrays[] = $event->toArray();
            }
        }
        echo json_encode($output_arrays);

    }



    public function todoList() {



        $etkinlikler = [];
        return $this->view("etkinlik/todo",[
            'etkinlikler'=> $etkinlikler
        ]);
    }



}