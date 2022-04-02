if("token" in localStorage){
    var reqAuth = new ReqJS("/api/auth?token="+localStorage.getItem("token"),"GET")
    reqAuth.send((r,s) => {
        if(s == 200){
            localStorage.setItem("accid",JSON.parse(r).accid)
            var reqPage = new ReqJS("/pages?page=home","GET")
            reqPage.send((r2,s2) => {
                if(s2 == 200){
                    document.write(r2)
                }
            })
        }else{
            location.href = "/";
        }
    })
}
