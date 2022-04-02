<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/_css/global.css">
    <link rel="stylesheet" href="/_css/tweet.css">
    <title>Profile</title>
</head>
<body>
    <div class="master-form">
        <div class="back"></div>
        <div class="new-form">
            <input type="hidden" class="mf-id">
            <input type="text" class="input mf-comment">
            <button class="mf-submit"></button>
        </div>
    </div>
    <button onclick="location.href = '/home'" style="margin-left: 100px;width:100px;margin-top:30px;">back</button>
    <button onclick="location.href = '/logout'" style="margin-left: 100px;width:100px;;margin-top:30px;">logout</button>
    <div class="main-info">
        <div>
            <input type="file" class="ch-pfp" style="position: absolute; width: 200px;height:200px;opacity: 0;">
            <img src="" class="mi-img">
        </div>
        <div class="mi-text">
            <div class="tweet" onclick="onEditName()"></div>
            <div class="poster" onclick="onEditBio()"></div>
        </div>
    </div>
    <div class="comments bot-info">
        <div style="display: flex;justify-content: space-between;">
            <h3>Tweets</h3>
            <div class="">
                <button class="add-comment" onclick="onAdd()">Add</button>
            </div>
        </div>
        
        <div class="box-list">
            
        </div>
    </div>
</body>
</html>
<script>
const boxList = document.querySelector(".box-list")

function loadData(){
    var reqData = new ReqJS("/api/profile","POST")
    reqData.setData([
        ["token",localStorage.getItem("token")]
    ])
    reqData.send((r,s) => {
        if(s == 200){
            localStorage.setItem("profile",r)

            let data = JSON.parse(localStorage.getItem("profile"))
            document.querySelector(".mi-img").src = data.image;
            document.querySelector(".poster").innerHTML = data.bio;
            document.querySelector(".tweet").innerHTML = data.name;
            boxList.innerHTML = ""

            let tmp = "";
            
            for(let i in data.tweet){
                tmp += `<div class="comment-box box"><div class="lcb"><h5>`+data.tweet[i]+`</h5></div><div class="comment-act">`;
                tmp += `<button class="btn" onclick="onEdit(`+i+`)">Edit</button><button class="btn" onclick="delComment(`+i+`)">Delete</button></div></div>`;
            }
            
            boxList.innerHTML = tmp;
        }
    })
}

function delComment(id){
    var req = new ReqJS("/api/tweet/del","POST")
    req.setData([
        ["token",localStorage.getItem("token")],
        ["t",id]
    ])

    req.send((r,s) => {
        if(s == 200){
            loadData()
        }else{
            location.href = "/";
        }
    })
}

document.querySelector(".back").addEventListener("click",() => {
    document.querySelector(".master-form").style.display = "none";
})

document.querySelector(".ch-pfp").addEventListener("change",() => {
    var req = new ReqJS("/api/profile/pfp","POST")
        req.setData([
            ["token",localStorage.getItem("token")],
            ["pfp",document.querySelector(".ch-pfp").files[0]]
        ])

        req.send((r,s) => {
            if(s == 200){
                loadData()
            }else{
                alert("Failed to change profile picture");
            }
        })
})

document.querySelector(".mf-submit").addEventListener("click",() => {
    let comment = document.querySelector(".mf-comment").value;
    const mfs = document.querySelector(".mf-submit").innerHTML;
    
    if(mfs == "Edit"){
        var req = new ReqJS("/api/tweet/edit","POST")
        req.setData([
            ["token",localStorage.getItem("token")],
            ["t",document.querySelector(".mf-id").value],
            ["tweet",document.querySelector(".mf-comment").value]
        ])

        req.send((r,s) => {
            if(s == 200){
                loadData()
                document.querySelector(".mf-comment").value = "";
                document.querySelector(".master-form").style.display = "none";
            }else{
                location.href = "/";
            }
        })
    }else if(mfs == "Add"){
        var req = new ReqJS("/api/tweet/add","POST")
        req.setData([
            ["token",localStorage.getItem("token")],
            ["tweet",document.querySelector(".mf-comment").value]
        ])

        req.send((r,s) => {
            if(s == 200){
                loadData()
                document.querySelector(".mf-comment").value = "";
                document.querySelector(".master-form").style.display = "none";
            }else{
                location.href = "/";
            }
        })
    }else if(mfs == "Edit Name"){
        var req = new ReqJS("/api/profile/name","POST")
        req.setData([
            ["token",localStorage.getItem("token")],
            ["name",document.querySelector(".mf-comment").value]
        ])

        req.send((r,s) => {
            if(s == 200){
                loadData()
                document.querySelector(".mf-comment").value = "";
                document.querySelector(".master-form").style.display = "none";
            }else{
                alert("Failed to edit name")
            }
        })
    }else if(mfs == "Edit Bio"){
        var req = new ReqJS("/api/profile/bio","POST")
        req.setData([
            ["token",localStorage.getItem("token")],
            ["bio",document.querySelector(".mf-comment").value]
        ])

        req.send((r,s) => {
            if(s == 200){
                loadData()
                document.querySelector(".mf-comment").value = "";
                document.querySelector(".master-form").style.display = "none";
            }else{
                location.href = "/";
            }
        })
    }
})

function onEdit(id){
    document.querySelector(".mf-submit").innerHTML = "Edit";
    document.querySelector(".master-form").style.display = "grid";
    document.querySelector(".mf-id").value = id;
}

function onAdd(){
    document.querySelector(".mf-submit").innerHTML = "Add";
    document.querySelector(".master-form").style.display = "grid";
}

function onEditName(){
    document.querySelector(".mf-submit").innerHTML = "Edit Name";
    document.querySelector(".master-form").style.display = "grid";
}

function onEditBio(){
    document.querySelector(".mf-submit").innerHTML = "Edit Bio";
    document.querySelector(".master-form").style.display = "grid";
}

loadData()

</script>