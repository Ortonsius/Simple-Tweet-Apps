if("token" in localStorage && "t" in localStorage){
    var reqAuth = new ReqJS("/api/auth?token="+localStorage.getItem("token"),"GET")
    reqAuth.send((r,s) => {
        if(s == 200){
            localStorage.setItem("accid",JSON.parse(r).accid)
            var reqData = new ReqJS("/api/t","POST")
            reqData.setData([
                ["token",localStorage.getItem("token")],
                ["t",localStorage.getItem("t")]
            ])
            reqData.send((r3,s3) => {
                if(s3 == 200){
                    localStorage.setItem("tweet",r3)
                    var reqPage = new ReqJS("/pages?page=tweet","GET")
                    reqPage.send((r2,s2) => {
                        if(s2 == 200){
                            document.write(r2)
                            return;
                        }
                    })
                }
            })
        }else{
            location.href = "/";
        }
    })
}
