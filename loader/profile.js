if("token" in localStorage){
    var reqAuth = new ReqJS("/api/auth?token="+localStorage.getItem("token"),"GET")
    reqAuth.send((r,s) => {
        if(s == 200){
            localStorage.setItem("accid",JSON.parse(r).accid)
            var reqData = new ReqJS("/api/profile","POST")
            reqData.setData([
                ["token",localStorage.getItem("token")]
            ])
            reqData.send((r,s) => {
                if(s == 200){
                    localStorage.setItem("profile",r)

                    var req = new ReqJS("/pages?page=profile","GET");
                    req.send((r,s) => {
                        if(s == 200){
                            document.write(r)
                        }
                    })
                }
            })
        }else{
            location.href = "/";
        }
    })
}
