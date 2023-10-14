<?php

namespace app\controller;

use app\Result;
use Exception;
use support\Db;

use support\Redis;
use support\Request;
use common\IdCreate;

class RegisterController
{
    public function byPhone(Request $request): \support\Response
    {
        $phone=$request->post('phone');
        $nick_name=$request->post('nick_name');
        $password=$request->post('password');
        $id=IdCreate::createOnlyId();
        $password=password_hash($password,PASSWORD_DEFAULT);
        try {
            $key='t_chat_account';
            if(!Redis::exists($key)){
                $last_account=DB::table('t_user')->select('account')->max('account');
                if(!$last_account){
                    $last_account='999999999';
                }
                Redis::set($key, $last_account);
            }
            Redis::incr($key);
            $account=Redis::get($key);
        }catch (Exception $e){
            $last_account=DB::table('t_user')->select('account')->max('account');
            if(!$last_account){
                $last_account='999999999';
            }
            $account=$last_account+1;
            $account=$account.'';
        }
        $num=mt_rand(1,1);
        DB::table('t_user')->insert([
            'id'=>$id,
            'account'=>$account,
            'nick_name'=>$nick_name,
            'avatar'=>Result::$baseURL.'/default_avatar/'.$num.'.jpg',
            'phone'=>$phone,
            'password'=>$password,
            'delete_status'=>1,
        ]);
        DB::table('t_message_pointer')->insert([
            'id'=>$id,
            'pointer'=>0
        ]);
        return Result::get(200,$account,"注册成功");
    }
}
