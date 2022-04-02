var req = new ReqJS("/pages?page=login","GET");
req.send((r,s) => {
    if(s == 200){
        document.write(r)
    }
})