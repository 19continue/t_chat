<?php

namespace app;

class Result
{
    public static $baseURL='http://localhost:8787';
    public static function get($code,$data,$msg): \support\Response
    {
       return json([
           'code'=>$code,
           'data'=>$data,
           'msg'=>$msg
       ]);
    }
}