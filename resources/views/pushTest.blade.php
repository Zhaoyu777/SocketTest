<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>websocket</title>
 </head>
 <body>
 <input id="text" value="">
 <input type="submit" value="send" onclick="start()">
 <input type="submit" value="close" onclick="close()">
<div id="msg"></div>
 <script>
 /**
 0：未连接

1：连接成功，可通讯

2：正在关闭

3：连接已关闭或无法打开
*/

    //创建一个webSocket 实例
    var webSocket  = new  WebSocket("ws://127.0.0.1:8889");

    webSocket.onerror = function (event){
        console.log(event);
        onError(event);
    };

    // 打开websocket
    webSocket.onopen = function (event){
        console.log(event);
        onOpen(event);
    };

    //监听消息
    webSocket.onmessage = function (event){
        onMessage(event);
    };


    webSocket.onclose = function (event){
        onClose(event);
    }

    //关闭监听websocket
    function onError(event){
        document.getElementById("msg").innerHTML = "<p>close</p>";
        console.log("error"+event.data);
    };

    function onOpen(event){
        console.log(event);
        console.log("open:"+sockState());
        document.getElementById("msg").innerHTML = "<p>Connect to Service</p>";
    };
    function onMessage(event){
        console.log("onMessage");
        document.getElementById("msg").innerHTML += "<p>response:"+event.data+"</p>"
    };

    function onClose(event){
        document.getElementById("msg").innerHTML = "<p>close</p>";
        console.log("close:"+sockState());
        webSocket.close();
    }

    function sockState(){
        var status = ['未连接','连接成功，可通讯','正在关闭','连接已关闭或无法打开'];
            return status[webSocket.readyState];
    }



 function start(event){
        console.log(webSocket);
        var msg = document.getElementById('text').value;
        document.getElementById('text').value = '';
        console.log("send:"+sockState());
        console.log("msg="+msg);
        webSocket.send("msg="+msg);
        document.getElementById("msg").innerHTML += "<p>request"+msg+"</p>"
    };

    function close(event){
        webSocket.close();
    }
 </script>
 </body>
</html>