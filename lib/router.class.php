<?php

class Router extends Master{
    private static function parseUrl(){
        for($i = 0; $i < strlen($_SERVER["REQUEST_URI"]); $i++){
            if($_SERVER["REQUEST_URI"][$i] == "?"){
                $url = $_SERVER["REQUEST_URI"];
                $real = substr($url,0,strpos($url,"?"));
                $arg = substr($url,strpos($url,"?") + 1,strlen($url) - 1);
                $ed = explode("&",$arg);
                foreach($ed as $i){
                    $kv = explode("=",$i);
                    if(count($kv) == 2){
                        $_GET[$kv[0]] = $kv[1];
                    }
                }
                return strtolower($real);
            }
        }
        return strtolower($_SERVER["REQUEST_URI"]);
    }

    public static function go(string $path,array $allowedMethod,$func,$script=[]){
        if(in_array($_SERVER["REQUEST_METHOD"],$allowedMethod)){
            if(self::parseUrl() == strtolower($path)){
                if(count($script) > 0){
                    foreach($script as $a){
                        echo "<script src=\"$a\"></script>";
                    }
                    echo "<script>";
                    $func();
                    echo "</script>";
                }else{
                    $func();
                }
                exit();
            }
        }
    }

    public static function api(string $path,array $allowedMethod,$func){
        if(in_array($_SERVER["REQUEST_METHOD"],$allowedMethod)){
            if(self::parseUrl() == strtolower($path)){
                http_response_code(404);
                header("Content-Type: application/json");
                $data = [];
                $func($data);
                echo json_encode($data);
                exit();
            }
        }
    }
}

?>