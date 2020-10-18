<?php
use Illuminate\Support;  // https://laravel.com/docs/5.8/collections - provides the collect methods & collections class
use LSS\Array2Xml;
require_once('classes/Exporter1.php');


class Controller {

    public function __construct() {

    }

    public function setArgs($args){
        $this->args = $args;
    }


//Here, an object of a class is used here that must have the methods XML(), JSON(), CSV() and HTML(). Also, this is where a helper class is assigned to the exporter object.
    public function setHelper($helper){
       
        $this->exporter = new Exporter();

        $this->exporter->setHelperClass($helper);

    }



    public function export($type, $format) {
        $data = [];

        switch ($type) {
            case 'playerstats':
                $data = $this->exporter->getPlayerStats($this->search());
                break;
            case 'players':
                $data = $this->exporter->getPlayers($this->search());
                break;
        }
        if (!$data) {
            exit("Error: No data found!");
        }
        return $this->exporter->format($data, $format);
    }


//The search() method have been created to avoid repitition of code. the search() method is used within the export method also.
    public function search(){

        $searchArgs = ['player', 'playerId', 'team', 'position', 'country'];
        $search = $this->args->filter(function($value, $key) use ($searchArgs) {
                    return in_array($key, $searchArgs);
        });

        return $search;
    }

}