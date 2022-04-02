class ReqJS{
    constructor(url,method){
        this.uri = url;
        this.method = method;
        this.data;

        this.head = [];

        this.msg = "ERROR";
        this.status;

        this.http = new XMLHttpRequest();
    }

    setData(data){
        var form = new FormData();
        data.forEach(i => {
            form.append(i[0],i[1]);
        })

        this.data = form;
    }

    setCustomError(msg){
        this.msg = msg;
    }

    _header(h){
        h.forEach(i => {
            this.http.setRequestHeader(i[0],i[1]);
        })
    }

    _checkUpRequirements(){
        if(this.uri == ""){
            this.msg = "URI: Error";
            return false;
        }else if(this.method == ""){
            this.msg = "METHOD: Error";
            return false;
        }else{
            return true;
        }
    }

    send(callback){
        var check = this._checkUpRequirements();
        if(!check){
            console.error(this.msg);
            return;
        }

        this.http.onreadystatechange = function(){
            if(this.readyState == XMLHttpRequest.DONE){
                callback(this.response,this.status);
            }
        }

        this.http.open(this.method,this.uri,true)
        this._header(this.head);
        this.http.send(this.data)
    }
}