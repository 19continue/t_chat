<?php

namespace app\controller;

use app\Result;
use common\IdCreate;
use Exception;
use support\Db;

use support\Redis;
use support\Request;
use Tinywan\Jwt\Exception\JwtTokenException;
use Tinywan\Jwt\Exception\JwtTokenExpiredException;
use Tinywan\Jwt\JwtToken;

class UserController
{
    public function createGroup(Request $request): \support\Response
    {
        $name=$request->post('name');
        $remark=$request->post('remark');
        try {
            $id = JwtToken::getCurrentId();
        }catch (JwtTokenExpiredException $e){
            return  Result::get(300,null,'token过期');
        }catch (JwtTokenException $e){
            return  Result::get(302,null,'token非法');
        }catch (Exception $e){
            return  Result::get(500,null,'系统出错');
        }
        $group_id=IdCreate::createOnlyId();
        try {
            $key='t_chat_group_number';
            if(!Redis::exists($key)){
                $last_number=DB::table('t_group')->select('number')->max('number');
                if(!$last_number){
                    $last_number='9999';
                }
                Redis::set($key, $last_number);
            }
            Redis::incr($key);
            $number=Redis::get($key);
        }catch (Exception $e){
            $last_number=DB::table('t_group')->select('number')->max('number');
            if(!$last_number){
                $last_number='9999';
            }
            $number=$last_number+1;
            $number=$number.'';
        }
        $num=mt_rand(1,1);
        $time=date_create()->format('Y-m-d H:i:s');
        DB::table('t_group')->insert([
            'id'=>$group_id,
            'number'=>$number,
            'manager_id'=>$id,
            'name'=>$name,
            'avatar'=>Result::$baseURL.'/group_default_avatar/'.$num.'.jpg',
            'remark'=>$remark,
            'create_time'=>$time,
            'delete_status'=>1,
        ]);
        $group=array('id'=>$group_id,
            'number'=>$number,
            'manager_id'=>$id,
            'name'=>$name,
            'avatar'=>Result::$baseURL.'/group_default_avatar/'.$num.'.jpg',
            'remark'=>$remark,
            'create_time'=>$time,
            'delete_status'=>1);
        $group=(object)$group;
        $user_group_id=IdCreate::createOnlyId();
        DB::table('t_user_group')->insert([
            'id'=>$user_group_id,
            'uid'=>$id,
            'gid'=>$group_id,
            'note_name'=>$name,
            'level'=>2,
            'create_time'=>$time,
            'delete_status'=>1,
        ]);
        return Result::get(200,$group,'创建成功');
    }
    public function updateInfo(Request $request): \support\Response
    {
        $nick_name=$request->post('nick_name');
        $phone=$request->post('phone');
        $email=$request->post('email');
        $sex=$request->post('sex');
        $signature=$request->post('signature');
        $location=$request->post('location');
        try {
            $id = JwtToken::getCurrentId();
        }catch (JwtTokenExpiredException $e){
            return  Result::get(300,null,'token过期');
        }catch (JwtTokenException $e){
            return  Result::get(302,null,'token非法');
        }catch (Exception $e){
            return  Result::get(500,null,'系统出错');
        }
        DB::table('t_user')->where('id','=',$id)->update([
            'nick_name' => $nick_name,
            'phone' => $phone,
            'email' => $email,
            'sex' => $sex,
            'signature' => $signature,
            'location' => $location,
        ]);
        return Result::get(200,null,'保存成功');
    }

    public function upload(Request $request): \support\Response
    {
        try {
            $id = JwtToken::getCurrentId();
        }catch (JwtTokenExpiredException $e){
            return  Result::get(300,null,'token过期');
        }catch (JwtTokenException $e){
            return  Result::get(302,null,'token非法');
        }catch (Exception $e){
            return  Result::get(500,null,'系统出错');
        }
        $file=$request->file('file');
        if ($file && $file->isValid()) {
            $file->move(public_path()."/user_avatar/$id.".$file->getUploadExtension());
            $path=Result::$baseURL."/user_avatar/$id.".$file->getUploadExtension();
            DB::table('t_user')->where('id','=',$id)->update(['avatar' => $path]);
            return Result::get(200,$path,'上传成功!');
        }
        return Result::get(600,null,'上传失败!');
    }
    public function getById(Request $request): \support\Response
    {
        try {
            JwtToken::getCurrentId();
        }catch (JwtTokenExpiredException $e){
            return  Result::get(300,null,'token过期');
        }catch (JwtTokenException $e){
            return  Result::get(302,null,'token非法');
        }catch (Exception $e){
            return  Result::get(500,null,'系统出错');
        }
        $id=$request->post('id');
        $data=DB::table('t_user')->find($id);
        if(!empty($data)) {
            $data->password = '';
            return Result::get(200,$data,'查询成功!');
        }
        else{
            return Result::get(600,null,'查询成功!');
        }
    }
    public function getGroupById(Request $request): \support\Response
    {
        try {
            JwtToken::getCurrentId();
        }catch (JwtTokenExpiredException $e){
            return  Result::get(300,null,'token过期');
        }catch (JwtTokenException $e){
            return  Result::get(302,null,'token非法');
        }catch (Exception $e){
            return  Result::get(500,null,'系统出错');
        }
        $groupId=$request->post('id');
        $data=DB::table('t_group')->find($groupId);
        if(!empty($data)) {
            return Result::get(200,$data,'查询成功!');
        }
        else{
            return Result::get(600,null,'查询成功!');
        }
    }
    public function getByAccount(Request $request): \support\Response
    {
        try {
            $id = JwtToken::getCurrentId();
        }catch (JwtTokenExpiredException $e){
            return  Result::get(300,null,'token过期');
        }catch (JwtTokenException $e){
            return  Result::get(302,null,'token非法');
        }catch (Exception $e){
            return  Result::get(500,null,'系统出错');
        }
        $account=$request->post('account');
        $data=DB::table('t_user')->where('account','like','%'.$account.'%')->get();
        return Result::get(200,$data,'查询成功!');
    }
    public function getByNumber(Request $request): \support\Response
    {
        try {
            $id = JwtToken::getCurrentId();
        }catch (JwtTokenExpiredException $e){
            return  Result::get(300,null,'token过期');
        }catch (JwtTokenException $e){
            return  Result::get(302,null,'token非法');
        }catch (Exception $e){
            return  Result::get(500,null,'系统出错');
        }
        $number=$request->post('number');
        $data=DB::table('t_group')->where('number','like','%'.$number.'%')->get();
        return Result::get(200,$data,'查询成功!');
    }
    public function getAllFriend(Request $request): \support\Response
    {
        try {
            $id = JwtToken::getCurrentId();
        }catch (JwtTokenExpiredException $e){
            return  Result::get(300,null,'token过期');
        }catch (JwtTokenException $e){
            return  Result::get(302,null,'token非法');
        }catch (Exception $e){
            return  Result::get(500,null,'系统出错');
        }
        $data=DB::table('t_friend')
            ->join('t_user', 't_friend.friend_id', '=', 't_user.id')
            ->where('t_friend.uid',$id)
            ->where('t_user.delete_status',1)
            ->select('t_user.id', 't_user.account', 't_user.nick_name',
                't_user.avatar','t_user.phone','t_user.email','t_user.sex','t_user.age',
                't_user.level','t_user.signature','t_user.location','t_user.create_time')
            ->get();
        return Result::get(200,$data,'查询成功!');
    }
    public function getAllGroup(Request $request): \support\Response
    {
        try {
            $id = JwtToken::getCurrentId();
        }catch (JwtTokenExpiredException $e){
            return  Result::get(300,null,'token过期');
        }catch (JwtTokenException $e){
            return  Result::get(302,null,'token非法');
        }catch (Exception $e){
            return  Result::get(500,null,'系统出错');
        }
        $data=DB::table('t_user_group')
            ->join('t_group', 't_group.id', '=', 't_user_group.gid')
            ->where('t_user_group.uid',$id)
            ->select('t_group.*','t_user_group.note_name')
            ->get();
        return Result::get(200,$data,'查询成功!');
    }
    public function getGroupMembers(Request $request): \support\Response
    {
        try {
            JwtToken::getCurrentId();
        }catch (JwtTokenExpiredException $e){
            return  Result::get(300,null,'token过期');
        }catch (JwtTokenException $e){
            return  Result::get(302,null,'token非法');
        }catch (Exception $e){
            return  Result::get(500,null,'系统出错');
        }
        $groupId=$request->post('id');
        $data=DB::table('t_user_group')
            ->join('t_user', 't_user.id', '=', 't_user_group.uid')
            ->where('t_user_group.gid',$groupId)
            ->select('t_user.*')
            ->get();
        return Result::get(200,$data,'查询成功!');
    }
    public function getAllRequest(Request $request): \support\Response
    {
        try {
            $id = JwtToken::getCurrentId();
        }catch (JwtTokenExpiredException $e){
            return  Result::get(300,null,'token过期');
        }catch (JwtTokenException $e){
            return  Result::get(302,null,'token非法');
        }catch (Exception $e){
            return  Result::get(500,null,'系统出错');
        }
        $data1=DB::table('t_request')
            ->join('t_user as user1','t_request.from_id','=','user1.id')
            ->join('t_user as user2','t_request.to_id','=','user2.id')
            ->select('t_request.*','user1.avatar as from_avatar','user2.avatar as to_avatar','user1.nick_name as from_name','user2.nick_name as to_name')
            ->where('t_request.from_id','=',$id)
            ->orWhere('t_request.to_id', '=',$id)
            ->get();
        $data2=DB::table('t_request')
            ->join('t_group','t_request.group_id','=','t_group.id')
            ->join('t_user as user1','t_request.from_id','=','user1.id')
            ->leftJoin('t_user as user2','t_request.deal_by','=','user2.id')
            ->select('t_request.*','t_group.avatar as group_avatar','t_group.name as group_name','user1.avatar as from_avatar','user1.nick_name as from_name','user2.avatar as to_avatar','user2.nick_name as to_name')
            ->where('t_request.from_id','=',$id)
            ->get();
        $latestPosts = DB::table('t_user_group')
            ->select('t_user_group.gid')
            ->where('t_user_group.uid', '=', $id)
            ->where('t_user_group.level', '>', 0);
        $data3=DB::table('t_request')->joinSub($latestPosts,'t_user_group', function ($join) {
            $join->on('t_user_group.gid', '=', 't_request.group_id');
             })->join('t_group','t_request.group_id','=','t_group.id')
            ->join('t_user as user1','t_request.from_id','=','user1.id')
            ->leftJoin('t_user as user2','t_request.deal_by','=','user2.id')
            ->select('t_request.*','t_group.avatar as group_avatar','t_group.name as group_name','user1.avatar as from_avatar','user1.nick_name as from_name','user2.avatar as to_avatar','user2.nick_name as to_name')
            ->get();
        foreach ($data2 as $k=>$v){
            $data1->push($v);
        }
        foreach ($data3 as $k=>$v){
            $data1->push($v);
        }
        return Result::get(200,$data1,'查询成功!');
    }
    public function getMessages(Request $request): \support\Response
    {
        $data=array();
        try {
            $id = JwtToken::getCurrentId();
        }catch (JwtTokenExpiredException $e){
            return  Result::get(300,null,'token过期');
        }catch (JwtTokenException $e){
            return  Result::get(302,null,'token非法');
        }catch (Exception $e){
            return  Result::get(500,null,'系统出错');
        }
        try {
            $keys=Redis::keys("user_messages:".$id.":*");
            foreach ($keys as $k=>$v){
                $data[]=json_decode(Redis::get($v));
            }
        }catch (Exception $e){
            $data=DB::table('t_message')
                ->where('t_message.id','>',function ($query,$id) {
                    $query->select('pointer')
                        ->from('t_message_pointer')
                        ->where('t_message_pointer.id', $id)
                        ->limit(1);
                })
                ->whereRaw('(t_message.from_id='.$id.' or t_message.to_id='.$id.')')
                ->get();
            return Result::get(200,$data,'查询成功!');
        }
        return Result::get(200,$data,'查询成功!');
    }
    public function getGroupMessages(Request $request): \support\Response
    {
        $groupId=$request->post('id');
        $data=array();
        try {
            $id = JwtToken::getCurrentId();
        }catch (JwtTokenExpiredException $e){
            return  Result::get(300,null,'token过期');
        }catch (JwtTokenException $e){
            return  Result::get(302,null,'token非法');
        }catch (Exception $e){
            return  Result::get(500,null,'系统出错');
        }
        try {
            $keys=Redis::keys("group_message:".$groupId.":*");
            foreach ($keys as $k=>$v){
                $data[]=json_decode(Redis::get($v));
            }
        }catch (Exception $e){
            $data=DB::table('t_message')
                ->where('t_message.id','>',function ($query,$id) {
                    $query->select('pointer')
                        ->from('t_message_pointer')
                        ->where('t_message_pointer.id', $id)
                        ->limit(1);
                })
                ->where('t_message.to_id',$groupId)
                ->get();
            return Result::get(200,$data,'查询成功!');
        }
        return Result::get(200,$data,'查询成功!');
    }
    public function getNews(Request $request): \support\Response
    {
        $data=array();
        try {
            $id = JwtToken::getCurrentId();
        }catch (JwtTokenExpiredException $e){
            return  Result::get(300,null,'token过期');
        }catch (JwtTokenException $e){
            return  Result::get(302,null,'token非法');
        }catch (Exception $e){
            return  Result::get(500,null,'系统出错');
        }
        try {
            $keys=Redis::keys("user_news:".$id.":*");
            foreach ($keys as $k=>$v){
                $data[]=json_decode(Redis::get($v));
            }
        }catch (Exception $e){
            return Result::get(200,$data,'查询成功!');
        }
        return Result::get(200,$data,'查询成功!');
    }
}
