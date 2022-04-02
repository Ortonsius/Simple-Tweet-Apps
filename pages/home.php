<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/_css/global.css">
    <link rel="stylesheet" href="/_css/home.css">
    <title>Home</title>
</head>
<body>
    <button onclick="location.href = '/profile'" style="margin-left: 100px;width:100px;padding:5px 0;margin-top:30px;">Profile</button>
    <div class="upper">
        <input type="text" class="search input" placeholder="Search ...">
    </div>
    <div class="bottom">
    </div>
</body>
</html>
<script>

var queryUrl = new URLSearchParams(window.location.search)
if(queryUrl.get("sch") != ""){
    var req = new ReqJS("/api/tweet","POST")
    req.setData([
        ["token",localStorage.getItem("token")],
        ["tag",queryUrl.get("sch")],
    ])
    req.send((r,s) => {
        if(s == 200){
            allTweet.innerHTML = "";
            let data = JSON.parse(r)
            for(let key in data){
                allTweet.innerHTML += `<div class="box" onclick="go2Tweet('`+key+`')">`+data[key]+`</div>`;
            }
            return;
        }
        location.href = "/";
    })
}

const searchTweet = document.querySelector(".search")
const allTweet = document.querySelector(".bottom")

function go2Tweet(id){
    localStorage.setItem('t',id);
    location.href='/tweet';
}

searchTweet.addEventListener("keydown",() => {
    var req = new ReqJS("/api/tweet","POST")
    req.setData([
        ["token",localStorage.getItem("token")],
        ["tag",searchTweet.value],
    ])
    req.send((r,s) => {
        if(s == 200){
            allTweet.innerHTML = "";
            let data = JSON.parse(r)
            for(let key in data){
                allTweet.innerHTML += `<div class="box" onclick="go2Tweet('`+key+`')">`+data[key]+`</div>`;
            }
            return;
        }
        location.href = "/";
    })
})

searchTweet.addEventListener("keyup",() => {
    var req = new ReqJS("/api/tweet","POST")
    req.setData([
        ["token",localStorage.getItem("token")],
        ["tag",searchTweet.value],
    ])
    req.send((r,s) => {
        if(s == 200){
            allTweet.innerHTML = "";
            let data = JSON.parse(r)
            for(let key in data){
                allTweet.innerHTML += `<div class="box" onclick="go2Tweet('`+key+`')">`+data[key]+`</div>`;
            }
            return;
        }
        location.href = "/";
    })
})

</script>