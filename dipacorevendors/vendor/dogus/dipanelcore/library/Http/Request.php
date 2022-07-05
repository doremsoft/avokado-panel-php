<?php

namespace Dipa\Http;

/**
 *
 * @author Doğuş DİCLE
 */
class Request {

    private $get;
    private $post;
    private $method;

    public function __construct() {

        $this->init();
    }

    public function init() {

        $this->get = $_GET;
        $this->post = $_POST;
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function set($key, $val) {

        switch ($this->method) {

            case 'POST':
                $this->post[$key] = $val;

                break;

            case 'GET':

                $this->get[$key] = $val;


                break;

            default:

                break;
        }
    }

    public function get($key) {

        return isset($this->get[$key]) ? $this->get[$key] : NULL;
    }

    public function post($key) {

        return isset($this->post[$key]) ? $this->post[$key] : NULL;
    }

    public function inputFilter($data) {

        return $data;
    }

    public function returnGet() {

        return $this->get;
    }

    public function returnPost() {

        return $this->post;
    }

    public function input($name, $filter = NULL) {

        switch ($this->method) {

            case 'POST':
                $result = $this->post($name);
                return $filter != NULL ? $this->inputFilter($result) : $result;
                break;

            case 'GET':
                $result = $this->get($name);

                return $filter != NULL ? $this->inputFilter($result) : $result;

                break;

            default:
                return NULL;
                break;
        }
    }

    public function has($name) {

        switch ($this->method) {

            case 'POST':
                $result = $this->post($name);
                return $result != NULL ? true : false;
                break;

            case 'GET':
                $result = $this->get($name);

                return $result != NULL ? true : false;

                break;

            default:
                return NULL;
                break;
        }
    }

    public function getAll() {
        switch ($this->method) {

            case 'POST':
                return $this->post;

                break;

            case 'GET':
                return $this->get;


                break;

            default:
                return NULL;
                break;
        }
    }

    public function append($name, $value, $type = NULL) {


        if ($type != NULL) {

            switch (strtoupper($type)) {
                case 'POST':

                    $_POST[$name] = $value;
                    break;

                case 'GET':

                    $_GET[$name] = $value;
                    break;
            }
        } else {

            switch ($this->method) {

                case 'POST':

                    $_POST[$name] = $value;
                    break;

                case 'GET':

                    $_GET[$name] = $value;
                    break;
            }
        }

        $this->init();

        return $this;
    }

    public static function set_csrf() {

        $token = md5(time());

        $_SESSION["csrftoken"] = $token;

        return $token;
    }

    public function csrf_control() {
        
             return true;

        if (isset($_SERVER['HTTP_REFERER'])) {

            $guvenliler = [
                'http://localhost/avokado/web/avokadopanel/login',
                 'http://localhost/avokado/web/avokadopanel',
                'http://avokadoyazilim.com/abone/login',
                'https://avokadoyazilim.com/abone/login',
                'http://www.avokadoyazilim.com/abone/login',
                'https://www.avokadoyazilim.com/abone/login',
            ];
            
            
           if (isset($_SERVER["HTTP_HOST"])) {

    if ($_SERVER["HTTP_HOST"] == "localhost") {
        
        return true;
    }
    }

            if (!in_array($_SERVER['HTTP_REFERER'], $guvenliler)) {

                $headers = apache_request_headers();


                if (isset($headers["csrftoken"])) {

                    if ($_SESSION["csrftoken"] == $headers["csrftoken"]) {

                        return true;
                    } else {
                        \Dipa\View::render("error/402");
                        die();
                    }
                } else {

                    if ($token = $this->input("csrftoken")) {

                        if (isset($_SESSION["csrftoken"])) {

                            if ($_SESSION["csrftoken"] == $token) {

                                if ($this->has("resettoken")) {

                                    self::set_csrf();
                                }
                                return true;
                            } else {
                                \Dipa\View::render("error/402");
                                die();
                            }
                        } else {
                            \Dipa\View::render("error/402");
                            die();
                        }
                    } else {

                        \Dipa\View::render("error/402");
                        die();
                    }
                }
            }
        }
    }

}
