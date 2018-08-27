<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Models\User;
use App\Models\Admin;

class IndexController extends BaseController
{
    public function index()
    {
    	$user = new Admin;
    	$userName = $user->createUser();

    	return view('pushTest');//response()->json($userName);
    }

    public function realTimeTest()
    {
		set_time_limit(0);  //这句很重要, 不至于运行超时
		while (true) {
		    if (true) {
		        var_dump('78888');
		        break;
			}
			var_dump('2333');
			usleep(200000);      //避免太过频繁的查询
		}

		echo '123';
    }

    public function push()
    {
    	//$tcp = getprotobyname("tcp");
	    //创建一个socket套接流
	    $socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
	    /****************设置socket连接选项，这两个步骤你可以省略*************/
	     //接收套接流的最大超时时间1秒，后面是微秒单位超时时间，设置为零，表示不管它
	    socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 1, "usec" => 0));
	     //发送套接流的最大超时时间为6秒
	    socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec" => 6, "usec" => 0));
	    /****************设置socket连接选项，这两个步骤你可以省略*************/

	    //连接服务端的套接流，这一步就是使客户端与服务器端的套接流建立联系

	    if(socket_connect($socket,'127.0.0.1',8889) == false){
	        echo 'connect fail massege:'.socket_strerror(socket_last_error());
	    }else{
	        $message = 'l love you 我爱你 socket';
	        //转为GBK编码，处理乱码问题，这要看你的编码情况而定，每个人的编码都不同
	        $message = mb_convert_encoding($message,'GBK','UTF-8');
	        //向服务端写入字符串信息
var_dump(socket_write($socket,$message,strlen($message)));
	        if(socket_write($socket,$message,strlen($message)) == false){
	            echo 'fail to write'.socket_strerror(socket_last_error());

	        }else{
	            //echo 'client write success'.PHP_EOL;
	            //读取服务端返回来的套接流信息                  
	             // while($callback = socket_read($socket,1024)){
	             //     echo 'server return message is:'.PHP_EOL.$callback;
	             // }
	        }
	    }


	    socket_close($socket);//工作完毕，关闭套接流
  	    return response()->json('122');
    }

}
