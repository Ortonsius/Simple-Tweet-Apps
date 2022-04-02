<?php

class DB extends Master{
    public static $dbname = "ukk";
    public static $dbuser = "root";
    public static $dbpwd = "";

    public static function query($stmt,$arg=[]){
        $pdo = new PDO("mysql:dbname=".DB::$dbname.";",DB::$dbuser,DB::$dbpwd);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $s = $pdo->prepare($stmt);
        $s->execute($arg);
        return $s;
    }
}

?>