<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/_css/global.css">
    <link rel="stylesheet" href="/_css/tweet.css">
    <title>Tweet</title>
</head>
<body>
    <div class="master-form">
        <div class="back"></div>
        <div class="new-form">
            <input type="hidden" class="mf-id">
            <input type="text" class="input mf-comment" placeholder="Comment">
            <button class="mf-submit"></button>
        </div>
    </div>
    <button onclick="location.href = '/home'" style="margin-left: 100px;width:100px;;margin-top:30px;">Back</button>
    <div class="main-info">
        <img src="" class="mi-img">
        <div class="mi-text">
            <div class="tweet"></div>
            <div class="poster"></div>
        </div>
    </div>
    <div class="comments bot-info">
        <div style="display: flex;justify-content: space-between;">
            <h3>Comments</h3>
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

function filterTag(text){
    var ntext = text.split(" ")
    for(var i = 0;i < ntext.length; i++){
        if(ntext[i].length > 0){
            if(ntext[i][0] == "#"){
                ntext[i] = `<div class='clickable' onclick="location.href = '/home?sch=`+ntext[i].replaceAll("#","")+`';">`+ntext[i]+`</div>`;
            }
        }
    }
    return ntext.join(" ")
}

function loadData(){
    let data = JSON.parse(localStorage.getItem("tweet"))

    var nt = filterTag(data.tweet)

    document.querySelector(".mi-img").src = data.poster.image;
    document.querySelector(".poster").innerHTML = data.poster.name;
    document.querySelector(".tweet").innerHTML = nt;
    boxList.innerHTML = ""

    let tmp = "";
    
    for(let i in data.comment){
        tmp += `<div class="comment-box box"><div class="lcb"><p>`+data.comment[i].name+`</p><h5>`+filterTag(data.comment[i].comment)+`</h5></div><div class="comment-act">`;
        if(data.comment[i].accid == localStorage.getItem("accid")){
            tmp += `<button class="btn" onclick="onEdit(`+i+`)">Edit</button><button class="btn" onclick="delComment(`+i+`)">Delete</button></div></div>`;
        }else{
            tmp += `</div></div>`;
        }

    }
    
    boxList.innerHTML = tmp;
}

function delComment(cid){
    var req = new ReqJS("/api/tweet/c/del","POST")
    req.setData([
        ["token",localStorage.getItem("token")],
        ["cid",cid],
        ["t",localStorage.getItem("t")]
    ])

    req.send((r,s) => {
        if(s == 200){
            localStorage.setItem("tweet",r)
            loadData()
        }else{
            location.href = "/";
        }
    })
}

document.querySelector(".back").addEventListener("click",() => {
    document.querySelector(".master-form").style.display = "none";
})

document.querySelector(".mf-submit").addEventListener("click",() => {
    let comment = document.querySelector(".mf-comment").value;
    
    if(document.querySelector(".mf-submit").innerHTML == "Edit"){
        var req = new ReqJS("/api/tweet/c/edit","POST")
        req.setData([
            ["token",localStorage.getItem("token")],
            ["cid",document.querySelector(".mf-id").value],
            ["t",localStorage.getItem("t")],
            ["comment",document.querySelector(".mf-comment").value]
        ])

        req.send((r,s) => {
            if(s == 200){
                localStorage.setItem("tweet",r)
                loadData()
                document.querySelector(".mf-comment").value = "";
                document.querySelector(".master-form").style.display = "none";
            }else{
                location.href = "/";
            }
        })
    }else{
        var req = new ReqJS("/api/tweet/c/add","POST")
        req.setData([
            ["token",localStorage.getItem("token")],
            ["t",localStorage.getItem("t")],
            ["comment",document.querySelector(".mf-comment").value]
        ])

        req.send((r,s) => {
            if(s == 200){
                localStorage.setItem("tweet",r)
                loadData()
                document.querySelector(".mf-comment").value = "";
                document.querySelector(".master-form").style.display = "none";
            }else{
                location.href = "/";
            }
        })
    }
})

function onEdit(cid){
    document.querySelector(".mf-submit").innerHTML = "Edit";
    document.querySelector(".master-form").style.display = "grid";
    document.querySelector(".mf-id").value = cid;
}

function onAdd(){
    document.querySelector(".mf-submit").innerHTML = "Add";
    document.querySelector(".master-form").style.display = "grid";
}



loadData()

</script>