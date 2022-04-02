var req = new ReqJS("/pages?page=register","GET");
req.send((r,s) => {
    if(s == 200){
        document.write(r)
    }
})