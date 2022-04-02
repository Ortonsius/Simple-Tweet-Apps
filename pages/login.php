<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/_css/global.css">
    <link rel="stylesheet" href="/_css/login.css">
    <title>Login</title>
</head>
<body>
    <div class="form">
        <input type="text" placeholder="Username" class="input usr">
        <input type="password" placeholder="Password" class="input pwd">
        <button class="btn">Login</button>
        <a href="/register">register</a>
    </div>
</body>
</html>
<script>

const username = document.querySelector(".usr");
const password = document.querySelector(".pwd");
const loginBtn = document.querySelector(".btn");

loginBtn.addEventListener("click",() => {
    var req = new ReqJS("/api/login","POST")
    req.setData([
        ["usr",username.value],
        ["pwd",password.value]
    ]);
    req.send((r,s) => {
        let data = JSON.parse(r);
        if(s == 200){
            localStorage.setItem("token",data.token);
            location.href = "/home";
        }else{
            alert(data.msg);
        }
    })
})

</script>