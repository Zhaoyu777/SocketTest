<?php
/**
 * Created by xwx
 * Date: 2017/10/18
 * Time: 14:33
 */

class SocketService
{
    private $sockets;
    private $address  = '0.0.0.0';
    private $port = 8083;
    private $_sockets;
    public function __construct($address = '', $port='')
    {
            if(!empty($address)){
                $this->address = $address;
            }
            if(!empty($port)) {
                $this->port = $port;
            }
    }

    public function service(){
        //获取tcp协议号码。
        $tcp = getprotobyname("tcp");
        //创建服务端的socket套接流,net协议为IPv4，protocol协议为TCP
        $socket = socket_create(AF_INET,SOCK_STREAM,$tcp);
        /*绑定接收的套接流主机和端口,与客户端相对应*/
        if(socket_bind($socket,'0.0.0.0',8889) == false){
            echo 'server bind fail:'.socket_strerror(socket_last_error());
            /*这里的127.0.0.1是在本地主机测试，你如果有多台电脑，可以写IP地址*/
        }
        //监听套接流
        if(socket_listen($socket,4)==false){
            echo 'server listen fail:'.socket_strerror(socket_last_error());
        }

        return $this->_sockets = $socket;
    }

    public function run(){

        //$newClient = socket_accept($socket))
        //$line = trim(socket_read($newClient, 1024));
        //$this->handshaking($newClient, $line);
        //获取client ip
        //socket_getpeername ($newClient, $ip);
        //$clients[$ip] = $newClient;

        $this->service();
        $socket = $this->_sockets;
         $this->sockets[] = $socket;
        do{
            $sockets = $this->sockets;
            foreach ($sockets as $key => $_sock){
                /*接收客户端传过来的信息*/
                $accept_resource = socket_accept($socket);
                echo 'key:'.$accept_resource;
                if ($socket !== $_sock) {
                    $line = trim(socket_read($accept_resource, 1024));
                    $this->handshaking($accept_resource, $line);
                } else {
                    /*socket_accept的作用就是接受socket_bind()所绑定的主机发过来的套接流*/
                    echo 'asdf: '.$accept_resource;
                    if($accept_resource !== false){
                        /*读取客户端传过来的资源，并转化为字符串*/
                         $string = socket_read($accept_resource,1024);
                        /*socket_read的作用就是读出socket_accept()的资源并把它转化为字符串*/
                        $this->send($socket, $string);
                        echo 'server receive is :'.$string.PHP_EOL;//PHP_EOL为php的换行预定义常量
                        if($string != false){
                            $return_client = 'server receive is : '.$string.PHP_EOL;
                            /*向socket_accept的套接流写入信息，也就是回馈信息给socket_bind()所绑定的主机客户端*/
                            socket_write($accept_resource,$return_client,strlen($return_client));
                            /*socket_write的作用是向socket_create的套接流写入信息，或者向socket_accept的套接流写入信息*/
                        }else{
                            echo 'socket_read is fail';
                        }
                        /*socket_close的作用是关闭socket_create()或者socket_accept()所建立的套接流*/
                        socket_close($accept_resource);
                    }
                }
            }
        }while(true);
    }

    /**
     * 握手处理
     * @param $newClient socket
     * @return int  接收到的信息
     */
    public function handshaking($newClient, $line){

        $headers = array();
        $lines = preg_split("/\r\n/", $line);
        foreach($lines as $line)
        {
            $line = chop($line);
            if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
            {
                $headers[$matches[1]] = $matches[2];
            }
        }
        $secKey = $headers['Sec-WebSocket-Key'];
        $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        $upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "WebSocket-Origin: $this->address\r\n" .
            "WebSocket-Location: ws://$this->address:$this->port/websocket/websocket\r\n".
            "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
        return socket_write($newClient, $upgrade, strlen($upgrade));
    }

    /**
     * 握手处理
     * @param $newClient socket
     * @return int  接收到的信息
     */
    public function connect(){

        $this->service();
        $socket = $this->_sockets;
        $this->sockets[] = $socket;
        do{
            $sockets = $this->sockets;
            foreach ($sockets as $key => $_sock){
                if($accept_resource !== false){
                    /*接收客户端传过来的信息*/
                    $accept_resource = socket_accept($socket);
                    echo 'key:'.$accept_resource;
                    $line = trim(socket_read($accept_resource, 1024));
                    $this->handshaking($accept_resource, $line);
                }
            }
        }while(true);
    }

    /**
     * 解析接收数据
     * @param $buffer
     * @return null|string
     */
    public function message($buffer){
        $len = $masks = $data = $decoded = null;
        $len = ord($buffer[1]) & 127;
        if ($len === 126)  {
            $masks = substr($buffer, 4, 4);
            $data = substr($buffer, 8);
        } else if ($len === 127)  {
            $masks = substr($buffer, 10, 4);
            $data = substr($buffer, 14);
        } else  {
            $masks = substr($buffer, 2, 4);
            $data = substr($buffer, 6);
        }
        for ($index = 0; $index < strlen($data); $index++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }
        return $decoded;
    }

    /**
     * 发送数据
     * @param $newClinet 新接入的socket
     * @param $msg   要发送的数据
     * @return int|string
     */
    public function send($newClinet, $msg){
        $msg = $this->frame($msg);
        socket_write($newClinet, $msg, strlen($msg));
    }

    public function frame($s) {
        $a = str_split($s, 125);
        if (count($a) == 1) {
            return "\x81" . chr(strlen($a[0])) . $a[0];
        }
        $ns = "";
        foreach ($a as $o) {
            $ns .= "\x81" . chr(strlen($o)) . $o;
        }
        return $ns;
    }

    /**
     * 关闭socket
     */
    public function close(){
        return socket_close($this->_sockets);
    }
}

$sock = new SocketService();
$sock->run();