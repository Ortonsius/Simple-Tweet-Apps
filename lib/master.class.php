<?php

class Master{
    protected static function findChar($str,$char){
        for($i = 0; $i < strlen($str); $i++){
            if($char == $str[$i]){
                return $i;
            }
        }

        return -1;
    }

    public static function randStr($len,$exp=""){
        $ds = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890";
        $dslen = strlen($ds);
        
        $res = "";
        $x = 0;
        if($exp == ""){
            while($x < $len){
                $char = $ds[mt_rand(0,$dslen - 1)];
                $res .= $char;
                $x++;
            }
        }else{
            while($x < $len){
                $char = $ds[mt_rand(0,$dslen - 1)];
                if(self::FindChar($exp,$char) == -1){
                    $res .= $char;
                    $x++;
                }
            }
        }

        return $res;
    }

    public static function checkAuth($token){
        if($token != false){
            $queryOnline = DB::query("SELECT * FROM online WHERE token = ?",[$token])->fetchAll();
            if(count($queryOnline) == 1){
                return [true,$queryOnline[0]["accid"]];
            }
            return [false,""];
        }
        return [false,""];
    }

    public static function getTweet($t,$result){
        $queryTweet = DB::query("SELECT * FROM tweet WHERE tid = ?",[$t])->fetchAll();
        if(count($queryTweet) == 1){
            $dataTweet = $queryTweet[0];
            $queryAcc = DB::query("SELECT * FROM account WHERE accid = ?",[$dataTweet["accid"]])->fetchAll();
            if(count($queryAcc) == 1){
                $dataAcc = $queryAcc[0];
                $queryCom = DB::query("SELECT * FROM comment WHERE tid = ?",[$t])->fetchAll();
                $com = [];
                foreach($queryCom as $i){
                    $queryAccCom = DB::query("SELECT name FROM account WHERE accid = ?",[$i["accid"]])->fetchAll();
                    if(count($queryAccCom) == 1){
                        $com[$i["cid"]] = [
                            "accid" => $i["accid"],
                            "name" => $queryAccCom[0]["name"],
                            "comment" => $i["comment"]
                        ];
                    }

                }

                return [[
                    "tweet" => $dataTweet["tweet"],
                    "poster" => [
                        "name" => $dataAcc["name"],
                        "image" => $dataAcc["image"]
                    ],
                    "comment" => $com
                ],true];
                http_response_code(200);
            }
        }

        return [[],false];
    }

    public static function updateTag($tid){
        //  Query tweet dulu
        $queryTweet = DB::query("SELECT * FROM tweet WHERE tid = ?",[$tid])->fetchAll();
        if(count($queryTweet) == 1){
            // Cek tag pada tweet
            $text = $queryTweet[0]["tweet"];
            $splitText = explode(" ",$text);
            $splitTag = [];
            foreach($splitText as $i){
                if(strlen($i) > 0){
                    if($i[0] == "#"){
                        // Cek tag yang tidak dipakai pada tweet
                        if(!in_array(str_replace("#","",$i),$splitTag)){
                            array_push($splitTag,str_replace("#","",$i));
                        }
                    }
                }
            }

            // Cek tag pada komen
            $queryComment = DB::query("SELECT * FROM comment WHERE tid = ?",[$tid])->fetchAll();
            if(count($queryComment) > 0){
                foreach($queryComment as $j){
                    $splitComment = explode(" ",$j["comment"]);
                    foreach($splitComment as $k){
                        if(strlen($k) > 0){
                            if($k[0] == "#"){
                                // Cek tag yang tidak dipakai pada komen
                                if(!in_array(str_replace("#","",$k),$splitTag)){
                                    array_push($splitTag,str_replace("#","",$k));
                                }
                            }
                        }
                    }
                }
            }
            
            // Gabung tag
            $nTag = implode(",",$splitTag);
            DB::query("UPDATE tweet SET tag = ? WHERE tid = ?",[$nTag,$tid]);
        }
    }
}

?>