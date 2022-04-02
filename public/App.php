<?php

include_once "../lib/master.class.php";
include_once "../lib/router.class.php";
include_once "../lib/db.class.php";

// Loader JS Pre-Load

Router::go("/",["GET"],function(){
    include_once "../loader/login.js";
},["/_js/req.js"]);

Router::go("/register",["GET"],function(){
    include_once "../loader/reg.js";
},["/_js/req.js"]);

Router::go("/home",["GET"],function(){
    include_once "../loader/home.js";
},["/_js/req.js"]);

Router::go("/tweet",["GET"],function(){
    include_once "../loader/tweet.js";
},["/_js/req.js"]);

Router::go("/profile",["GET"],function(){
    include_once "../loader/profile.js";
},["/_js/req.js"]);

Router::go("/logout",["GET"],function(){
    include_once "../loader/logout.js";
},["/_js/req.js"]);





// Page Loader

Router::go("/pages",["GET"],function(){
    $page = isset($_GET["page"]) ? htmlspecialchars($_GET["page"]) : false;
    if($page == false){
        http_response_code(404);
        return;
    }

    $mapPages = [
        "login" => "../pages/login.php",
        "home" => "../pages/home.php",
        "tweet" => "../pages/tweet.php",
        "register" => "../pages/reg.php",
        "profile" => "../pages/profile.php"
    ];

    if(isset($mapPages[$page])){
        http_response_code(200);
        include_once $mapPages[$page];
        return;
    }
    http_response_code(404);
    return;
});






// API

Router::api("/api/login",["POST"],function(&$OUT){
    $username = isset($_POST["usr"]) ? htmlspecialchars($_POST["usr"]) : false;
    $password = isset($_POST["pwd"]) ? htmlspecialchars($_POST["pwd"]) : false;

    if($password == false || $username == false){
        $OUT["msg"] = "Invalid username/password";
        http_response_code(404);
        return;
    }

    $queryLogin = DB::query("SELECT * FROM account WHERE name = ? AND password = ?",[$username,$password]);
    $fetchedData = $queryLogin->fetchAll();
    if(count($fetchedData) == 1){
        while(true){
            $tmpid = Master::randStr(10);
            $queryCountOnline = DB::query("SELECT COUNT(*) FROM online WHERE token = ?",[$tmpid])->fetch()[0];
            if($queryCountOnline == 0){
                DB::query("INSERT INTO online VALUES(?,?)",[$tmpid,$fetchedData[0]["accid"]]);
                $OUT["token"] = $tmpid;
                http_response_code(200);
                return;   
            }
        }
    }else{
        $OUT["msg"] = "Invalid username/password";
        http_response_code(404);
        return;
    }
});

Router::api("/api/register",["POST"],function(&$OUT){
    $username = isset($_POST["usr"]) ? htmlspecialchars($_POST["usr"]) : false;
    $password = isset($_POST["pwd"]) ? htmlspecialchars($_POST["pwd"]) : false;

    if($password == false || $username == false){
        $OUT["msg"] = "Failed to create account";
        http_response_code(404);
        return;
    }

    $queryReg = DB::query("SELECT * FROM account WHERE name = ? AND password = ?",[$username,$password]);
    $fetchedData = $queryReg->fetchAll();
    if(count($fetchedData) == 0){
        DB::query("INSERT INTO account (name,password,image,bio) VALUES(?,?,'#','bio')",[$username,$password]);
        $queryAcc = DB::query("SELECT * FROM account WHERE name = ? AND password = ?",[$username,$password]);
        $fd = $queryAcc->fetchAll();
        while(true){
            $tmpid = Master::randStr(10);
            $queryCountOnline = DB::query("SELECT COUNT(*) FROM online WHERE token = ?",[$tmpid])->fetch()[0];
            if($queryCountOnline == 0){
                DB::query("INSERT INTO online VALUES(?,?)",[$tmpid,$fd[0]["accid"]]);
                $OUT["token"] = $tmpid;
                http_response_code(200);
                return;   
            }
        }
    }else{
        $OUT["msg"] = "Failed to create account";
        http_response_code(404);
        return;
    }
});

Router::api("/api/auth",["GET"],function(&$OUT){
    $token = isset($_GET["token"]) ? htmlspecialchars($_GET["token"]) : false;
    $data = Master::checkAuth($token);
    if($data[0]){
        $OUT["accid"] = $data[1];
        http_response_code(200);
        return;
    }
    http_response_code(404);
});

Router::api("/api/logout",["POST"],function(&$OUT){
    $token = isset($_POST["token"]) ? htmlspecialchars($_POST["token"]) : false;
    $result = Master::checkAuth($token);
    if($result[0]){
        DB::query("DELETE FROM online WHERE token = ?",[$token]);
        http_response_code(200);
        return;
    }
    $OUT["msg"] = "Failed to logout";
    http_response_code(404);
    return;
});

Router::api("/api/tweet",["POST"],function(&$OUT){
    $tag = isset($_POST["tag"]) ? htmlspecialchars($_POST["tag"]) : false;
    $token = isset($_POST["token"]) ? htmlspecialchars($_POST["token"]) : false;
    $result = Master::checkAuth($token);
    if($result[0]){
        if($tag != false){
            $queryTweet = DB::query("SELECT * FROM tweet")->fetchAll();
            foreach($queryTweet as $i){
                $allTag = explode(",",$i["tag"]);
                foreach($allTag as $j){
                    if(str_contains($j,$tag)){
                        $OUT[$i["tid"]] = $i["tweet"];
                    }
                }
            }
            http_response_code(200);
            return;
        }else{
            http_response_code(200);
            return;
        }
    }
    http_response_code(404);
    return;
});

Router::api("/api/t",["POST"],function(&$OUT){
    $t = isset($_POST["t"]) ? htmlspecialchars($_POST["t"]) : false;
    $token = isset($_POST["token"]) ? htmlspecialchars($_POST["token"]) : false;
    $result = Master::checkAuth($token);
    if($result[0] && $t != false){
        $data = Master::getTweet($t,$result);
        if($data[1]){
            http_response_code(200);
            $OUT = $data[0];
            return;
        }
    }
    http_response_code(404);
    return;
});

Router::api("/api/tweet/c/add",["POST"],function(&$OUT){
    $t = isset($_POST["t"]) ? htmlspecialchars($_POST["t"]) : false;
    $token = isset($_POST["token"]) ? htmlspecialchars($_POST["token"]) : false;
    $comment = isset($_POST["comment"]) ? htmlspecialchars($_POST["comment"]) : false;
    $result = Master::checkAuth($token);
    if($result[0] && false != $comment){
        DB::query("INSERT INTO comment (comment,tid,accid) VALUES (?,?,?)",[$comment,$t,$result[1]]);
        Master::updateTag($t);
        $data = Master::getTweet($t,$result);
        if($data[1]){
            http_response_code(200);
            $OUT = $data[0];
            return;
        }
    }
    http_response_code(404);
    return;
});

Router::api("/api/tweet/c/del",["POST"],function(&$OUT){
    $t = isset($_POST["t"]) ? htmlspecialchars($_POST["t"]) : false;
    $cid = isset($_POST["cid"]) ? htmlspecialchars($_POST["cid"]) : false;
    $token = isset($_POST["token"]) ? htmlspecialchars($_POST["token"]) : false;
    $result = Master::checkAuth($token);
    if($result[0] && $cid != false){
        DB::query("DELETE FROM comment WHERE accid = ? AND cid = ?",[$result[1],$cid]);
        Master::updateTag($t);
        $data = Master::getTweet($t,$result);
        if($data[1]){
            http_response_code(200);
            $OUT = $data[0];
            return;
        }
    }
    http_response_code(404);
    return;
});

Router::api("/api/tweet/c/edit",["POST"],function(&$OUT){
    $t = isset($_POST["t"]) ? htmlspecialchars($_POST["t"]) : false;
    $cid = isset($_POST["cid"]) ? htmlspecialchars($_POST["cid"]) : false;
    $token = isset($_POST["token"]) ? htmlspecialchars($_POST["token"]) : false;
    $comment = isset($_POST["comment"]) ? htmlspecialchars($_POST["comment"]) : false;
    $result = Master::checkAuth($token);
    if($result[0] && $cid != false){
        DB::query("UPDATE comment SET comment = ? WHERE accid = ? AND cid = ?",[$comment,$result[1],$cid]);
        Master::updateTag($t);
        $data = Master::getTweet($t,$result);
        if($data[1]){
            http_response_code(200);
            $OUT = $data[0];
            return;
        }
    }
    http_response_code(404);
    return;
});

Router::api("/api/profile",["POST"],function(&$OUT){
    $token = isset($_POST["token"]) ? htmlspecialchars($_POST["token"]) : false;

    $result = Master::checkAuth($token);
    if($result[0]){
        $fdAcc = DB::query("SELECT * FROM account WHERE accid = ?",[$result[1]])->fetchAll();
        if(count($fdAcc) == 1){
            $fdTweet = DB::query("SELECT * FROM tweet WHERE accid = ?",[$result[1]])->fetchAll();

            $varTweet = [];

            foreach($fdTweet as $i){
                $varTweet[$i["tid"]] = $i["tweet"];
                Master::updateTag($i["tid"]);
            }

            $OUT = [
                "name" => $fdAcc[0]["name"],
                "image" => $fdAcc[0]["image"],
                "bio" => $fdAcc[0]["bio"],
                "tweet" => $varTweet
            ];
            http_response_code(200);
            return;
        }
    }
    http_response_code(404);
    return;
});

Router::api("/api/profile/pfp",["POST"],function(&$OUT){
    $token = isset($_POST["token"]) ? htmlspecialchars($_POST["token"]) : false;
    $pfp = isset($_FILES["pfp"]) ? $_FILES["pfp"] : false;

    $result = Master::checkAuth($token);
    if($result[0]){
        if(in_array($pfp["type"],["image/jpeg","image/png","image/jpg"])){
            move_uploaded_file($pfp["tmp_name"],"../public/_image/".$pfp["name"]);
            http_response_code(200);
            DB::query("UPDATE account SET image = ? WHERE accid = ?",["/_image/".$pfp["name"],$result[1]]);
            return;
        }
        var_dump($pfp);
    }
    http_response_code(404);
    return;
});

Router::api("/api/profile/name",["POST"],function(&$OUT){
    $token = isset($_POST["token"]) ? htmlspecialchars($_POST["token"]) : false;
    $name = isset($_POST["name"]) ? $_POST["name"] : false;

    $result = Master::checkAuth($token);
    if($result[0] && $name != false){
        if(count(DB::query("SELECT * FROM account WHERE name = ?",[$name])->fetchAll()) == 0){
            DB::query("UPDATE account SET name = ? WHERE accid = ?",[$name,$result[1]]);
            http_response_code(200);
            return;
        }
    }
    http_response_code(404);
    return;
});

Router::api("/api/profile/bio",["POST"],function(&$OUT){
    $token = isset($_POST["token"]) ? htmlspecialchars($_POST["token"]) : false;
    $bio = isset($_POST["bio"]) ? $_POST["bio"] : false;

    $result = Master::checkAuth($token);
    if($result[0] && $bio != false){
        DB::query("UPDATE account SET bio = ? WHERE accid = ?",[$bio,$result[1]]);
        http_response_code(200);
        return;
    }
    http_response_code(404);
    return;
});

Router::api("/api/tweet/edit",["POST"],function(&$OUT){
    $t = isset($_POST["t"]) ? htmlspecialchars($_POST["t"]) : false;
    $token = isset($_POST["token"]) ? htmlspecialchars($_POST["token"]) : false;
    $tweet = isset($_POST["tweet"]) ? htmlspecialchars($_POST["tweet"]) : false;
    $result = Master::checkAuth($token);
    if($result[0] && $tweet != false){
        DB::query("UPDATE tweet SET tweet = ? WHERE accid = ? AND tid = ?",[$tweet,$result[1],$t]);
        Master::updateTag($t);
        http_response_code(200);
        return;
    }
    http_response_code(404);
    return;
});

Router::api("/api/tweet/del",["POST"],function(&$OUT){
    $t = isset($_POST["t"]) ? htmlspecialchars($_POST["t"]) : false;
    $token = isset($_POST["token"]) ? htmlspecialchars($_POST["token"]) : false;
    $result = Master::checkAuth($token);
    if($result[0] && $t != false){
        DB::query("DELETE FROM tweet WHERE accid = ? AND tid = ?",[$result[1],$t]);
        http_response_code(200);
        return;
    }
    http_response_code(404);
    return;
});

Router::api("/api/tweet/add",["POST"],function(&$OUT){
    $token = isset($_POST["token"]) ? htmlspecialchars($_POST["token"]) : false;
    $tweet = isset($_POST["tweet"]) ? htmlspecialchars($_POST["tweet"]) : false;
    $result = Master::checkAuth($token);
    if($result[0] && $tweet != false){
        DB::query("INSERT INTO tweet (accid,tweet) VALUES (?,?)",[$result[1],$tweet,]);
        http_response_code(200);
        return;
    }
    http_response_code(404);
    return;
});

?>