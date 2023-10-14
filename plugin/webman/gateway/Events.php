<?php

namespace plugin\webman\gateway;

use app\Result;
use common\IdCreate;
use Exception;
use GatewayWorker\Lib\Gateway;
use support\Db;
use support\Redis;
use Tinywan\Jwt\Exception\JwtTokenException;
use Tinywan\Jwt\Exception\JwtTokenExpiredException;
use Tinywan\Jwt\JwtToken;
use Workerman\Timer;

class Events
{
    public static function onWorkerStart($worker)
    {
    }

    public static function onConnect($client_id)
    {

    }

    public static function onWebSocketConnect($client_id, $data)
    {
        $online=array();
        try {
            $token=$data['get']['token'];
            if($token){
                $id = JwtToken::verify(1,$token)['extend']['id'];
                var_dump($id);
            }
            else{
                Gateway::closeClient($client_id,json_encode(array('code'=>101,'msg'=>'连接需要token')));
                return;
            }
            Gateway::bindUid($client_id, $id);
            $friends=DB::table('t_friend')
                ->join('t_user', 't_friend.friend_id', '=', 't_user.id')
                ->where('t_friend.uid',$id)
                ->where('t_user.delete_status',1)
                ->select('t_user.id')
                ->get();
            $groups=DB::table('t_user_group')
                ->where('uid',$id)
                ->select('gid')
                ->get();
            foreach ($friends as $key=>$value){
                if (Gateway::isUidOnline($value->id)) {
                    $online[] = $value->id;
                    Gateway::sendToUid($value->id, json_encode(array('code'=>105,'id'=>$id)));
                }
            }
            foreach ($groups as $key=>$value){
                Gateway::joinGroup($client_id, $value->gid);
            }
        }catch (JwtTokenExpiredException $e){
            Gateway::closeClient($client_id,json_encode(array('code'=>102,'msg'=>'token过期')));
        }catch (JwtTokenException $e){
            Gateway::closeClient($client_id,json_encode(array('code'=>103,'msg'=>'token非法')));
        }catch (Exception $e){
            Gateway::closeClient($client_id,json_encode(array('code'=>104,'msg'=>'连接出错')));
        }
        var_dump( Gateway::getAllClientIdCount());
        Gateway::sendToClient($client_id,json_encode(array('code'=>100,'data'=>$online,'msg'=>'连接成功')));
    }

    public static function onMessage($client_id, $message)
    {
        var_dump($message);
        $msg=json_decode($message);
        $id=Gateway::getUidByClientId($client_id);
        if($msg->code==120){
            if($msg->point){
                DB::table('t_message_pointer')->where('id','=',$msg->id)->update(['pointer'=>$msg->point]);
            }
        }
        elseif($msg->code==105){
            if (Gateway::isUidOnline($msg->id)) {
                Gateway::sendToUid($id, json_encode(array('code'=>105,'id'=>$msg->id)));
            }
        }
        elseif($msg->code==130){
            try {
                $key="user_news:".$id.":".$msg->data->id;
                Redis::set($key,json_encode($msg->data),3600*24*30);
            }catch (Exception $exception){
                var_dump($exception);
            }
        }
        elseif($msg->code==200){
            if($msg->pattern==0){
                $msg->id=IdCreate::createOnlyId();
                $msg->create_time=date_create()->format('Y-m-d H:i:s');
                if ($msg->type==0){
                    DB::table('t_message')->insert([
                        'id'=>$msg->id,
                        'type'=>$msg->type,
                        'pattern'=>$msg->pattern,
                        'from_id'=>$msg->from_id,
                        'to_id'=>$msg->to_id,
                        'content'=>$msg->content,
                        'create_time'=>$msg->create_time,
                        'delete_status'=>1,
                    ]);
                    Gateway::sendToUid($msg->to_id,json_encode($msg));
                    try {
                        $key1="user_messages:".$msg->from_id.":".$msg->to_id.":".$msg->id;
                        $key2="user_messages:".$msg->to_id.":".$msg->from_id.":".$msg->id;
                        Redis::set($key1,json_encode($msg),3600*24*30);
                        Redis::set($key2,json_encode($msg),3600*24*30);
                    }catch (Exception $exception){
                        var_dump($exception);
                    }
                }
            }
            elseif ($msg->pattern==1){
                $msg->id=IdCreate::createOnlyId();
                $time=date_create()->format('Y-m-d H:i:s');
                if ($msg->type==0){
                    DB::table('t_message')->insert([
                        'id'=>$msg->id,
                        'type'=>$msg->type,
                        'pattern'=>$msg->pattern,
                        'from_id'=>$msg->from_id,
                        'to_id'=>$msg->to_id,
                        'group_id'=>$msg->to_id,
                        'content'=>$msg->content,
                        'create_time'=>$time,
                        'delete_status'=>1,
                    ]);
                }
                $msg->create_time=$time;
                Gateway::sendToGroup($msg->to_id, json_encode($msg),[$client_id]);
                try {
                    $key="group_message:".$msg->to_id.":".$msg->from_id.":".$msg->id;
                    Redis::set($key,json_encode($msg),3600*24*30);
                }catch (Exception $exception){
                    var_dump($exception);
                }
            }
            DB::table('t_message_pointer')->where('id','=',$msg->from_id)->update(['pointer'=>$msg->id]);
        }elseif ($msg->code==300){
            if($msg->pattern==0){
                if($msg->status==0){
                    $rq=DB::table('t_request')->where('from_id',$msg->from_id)->where('to_id',$msg->to_id)->first();
                    if(empty($rq)){
                        $msg->id=IdCreate::createOnlyId();
                        DB::table('t_request')->insert([
                            'id'=>$msg->id,
                            'pattern'=>$msg->pattern,
                            'from_id'=>$msg->from_id,
                            'to_id'=>$msg->to_id,
                            'status'=>$msg->status,
                            'create_time'=>date_create()->format('Y-m-d H:i:s'),
                            'delete_status'=>1,
                        ]);
                        Gateway::sendToUid($msg->to_id,json_encode($msg));
                        Gateway::sendToUid($msg->from_id,json_encode(array('code'=>110,'msg'=>'好友请求发送成功')));
                    }else{
                        Gateway::sendToUid($msg->from_id,json_encode(array('code'=>110,'msg'=>'请勿重复发送好友请求')));
                    }
                }elseif ($msg->status==1){
                    DB::table('t_request')->where('id','=',$msg->id)->update([
                        'status'=>$msg->status,
                        'deal_by'=>$msg->to_id
                    ]);
                    $time=date_create()->format('Y-m-d H:i:s');
                    DB::table('t_friend')->insert([
                        'id'=>IdCreate::createOnlyId(),
                        'uid'=>$msg->from_id,
                        'friend_id'=>$msg->to_id,
                        'create_time'=>$time,
                        'delete_status'=>1,
                    ]);
                    DB::table('t_friend')->insert([
                        'id'=>IdCreate::createOnlyId(),
                        'uid'=>$msg->to_id,
                        'friend_id'=>$msg->from_id,
                        'create_time'=>$time,
                        'delete_status'=>1,
                    ]);
                    Gateway::sendToUid($msg->from_id, $message);
                }elseif ($msg->status==2){
                    DB::table('t_request')->where('id','=',$msg->id)->update([
                        'status'=>$msg->status,
                        'deal_by'=>$msg->to_id
                    ]);
                    Gateway::sendToUid($msg->from_id, $message);
                }else{
                    DB::table('t_request')->where('id','=',$msg->id)->update([
                        'status'=>$msg->status,
                        'deal_by'=>$msg->to_id
                    ]);
                }
            }elseif ($msg->pattern==1){
                if($msg->status==0){
                    $rq=DB::table('t_request')->where('from_id',$msg->from_id)->where('to_id',$msg->to_id)->first();
                    if(empty($rq)){
                        $msg->id=IdCreate::createOnlyId();
                        $group_id=null;
                        if(isset($msg->group_id)){
                            $group_id=$msg->group_id;
                        }
                        DB::table('t_request')->insert([
                            'id'=>$msg->id,
                            'pattern'=>$msg->pattern,
                            'from_id'=>$msg->from_id,
                            'to_id'=>$msg->to_id,
                            'group_id'=>$group_id,
                            'status'=>$msg->status,
                            'create_time'=>date_create()->format('Y-m-d H:i:s'),
                            'delete_status'=>1,
                        ]);
                        $managers=DB::table('t_user_group')->where('gid',$msg->to_id)->where('level','>',0)->get();
                        foreach ($managers as $k=>$v) {
                            Gateway::sendToUid($v->uid, json_encode($msg));
                        }
                        Gateway::sendToUid($msg->from_id,json_encode(array('code'=>110,'msg'=>'入群申请发送成功')));
                    }else{
                        Gateway::sendToUid($msg->from_id,json_encode(array('code'=>110,'msg'=>'请勿重复发送入群申请')));
                    }
                }elseif ($msg->status==1){
                    DB::table('t_request')->where('id','=',$msg->id)->update([
                        'status'=>$msg->status,
                        'deal_by'=>$id
                    ]);
                    $time=date_create()->format('Y-m-d H:i:s');
                    $group_id=IdCreate::createOnlyId();
                    DB::table('t_user_group')->insert([
                        'id'=>$group_id,
                        'uid'=>$msg->from_id,
                        'gid'=>$msg->to_id,
                        'level'=>0,
                        'create_time'=>$time,
                        'delete_status'=>1,
                    ]);
                    Gateway::sendToUid($msg->from_id, $message);
                    $managers=DB::table('t_user_group')->where('gid',$msg->to_id)->where('level','>',0)->get();
                    foreach ($managers as $k=>$v) {
                        Gateway::sendToUid($v->uid, $message);
                    }
                }elseif ($msg->status==2){
                    DB::table('t_request')->where('id','=',$msg->id)->update([
                        'status'=>$msg->status,
                        'deal_by'=>$id
                    ]);
                    Gateway::sendToUid($msg->from_id, $message);
                    $managers=DB::table('t_user_group')->where('gid',$msg->to_id)->where('level','>',0)->get();
                    foreach ($managers as $k=>$v) {
                        Gateway::sendToUid($v->uid, $message);
                    }
                }
            }
        }elseif ($msg->code==400){
            if($msg->pattern==0){
                if($msg->type==6){
                    if($msg->status==0){
                        if(Gateway::isUidOnline($msg->to_id)){
                            Gateway::sendToUid($msg->to_id,json_encode(array('code'=>400,'status'=>0,'from_id'=>$msg->from_id,'avatar'=>$msg->avatar,'peerId'=>$msg->peerId)));
                        }
                        else{
                            Gateway::sendToUid($msg->from_id,json_encode(array('code'=>400,'status'=>3)));
                        }
                    }elseif ($msg->status==4 || $msg->status==5){
                        Gateway::sendToUid($msg->to_id,json_encode(array('code'=>400,'status'=>$msg->status)));
                    }
                }
                elseif ($msg->type==7){
                    if($msg->status==1){
                        Gateway::sendToUid($msg->to_id,json_encode(array('code'=>400,'status'=>1)));
                    }
                    else if($msg->status==2 || $msg->status==6){
                        Gateway::sendToUid($msg->to_id,json_encode(array('code'=>400,'status'=>$msg->status)));
                    }
                }
            }
        }
    }

    public static function onClose($client_id)
    {

    }

}
