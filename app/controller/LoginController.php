<?php

namespace app\controller;

use app\Result;
use support\Db;

use support\Request;
use Tinywan\Jwt\Exception\JwtTokenException;
use Tinywan\Jwt\Exception\JwtTokenExpiredException;
use Tinywan\Jwt\JwtToken;

class LoginController
{
    public function byAccount(Request $request): \support\Response
    {
        $account=$request->post('account');
        $password=$request->post('password');
        $user=DB::table('t_user')->where('account','=',$account)->first();
        if($user){
            if($user->delete_status==0){
                return Result::get(600,null,'账号已被封禁');
            }
            if(password_verify($password,$user->password)){
                $point=DB::table('t_message_pointer')->select('pointer')->find($user->id);
                $token = JwtToken::generateToken(['id'=>$user->id]);
                $user->password='';
                $user->point=$point;
                $token['user']=$user;
                return Result::get(200,$token,'登录成功');
            }
            else{
                return Result::get(600,$user,'密码错误');
            }
        }
        return Result::get(600,$user,'账号不存在');
    }
    public function refresh(Request $request): \support\Response
    {
        try {
            $token= JwtToken::refreshToken();
        }catch (JwtTokenExpiredException $e){
           return  Result::get(301,null,'token过期');
        }catch (JwtTokenException $e){
           return Result::get(302,null,'token非法');
        }catch (Exception $e){
           return Result::get(500,null,'系统出错');
        }
        return Result::get(200,$token,'token刷新成功');
    }
}
