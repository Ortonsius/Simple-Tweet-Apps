if("token" in localStorage){
    var req = new ReqJS("/api/logout","POST")
    req.setData([
        ["token",localStorage.getItem("token")]
    ])
    req.send((r,s) => {
        if(s == 200){
            alert(2)
            localStorage.clear()
        }
    })
}
location.href = "/";