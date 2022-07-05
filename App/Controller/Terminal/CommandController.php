<?php
namespace App\Controller\Terminal;

use App\Controller\Import\xmlActionController;

class CommandController extends \Dipa\Controller
{

    private $argv;
    private $command;


    public function __construct($argv) {
        parent::__construct(false,false,"login",false,true);

        $this->command = $argv[1];
        $this->argv = $argv;
    }

    public function runCommand(){

        switch ($this->command){

            case "xmlimport":

                $this->executeXml($this->argv[2]);

                break;

            default:
                echo "commandnotfound";
                break;

        }
    }


    private function executeXml($id){



       $action = new xmlActionController(false,$id);

        $action->xmlImportFromTerminal($id);

    }





}
