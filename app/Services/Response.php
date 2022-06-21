<?php
namespace App\Services;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class Response
{
    static function success($data = null, $msg = 'success', $code = 200)
    {
        if($data instanceof AnonymousResourceCollection  && $data->resource instanceof LengthAwarePaginator){
            return self::response(self::page($data->resource),$msg,$code);

        }elseif($data instanceof LengthAwarePaginator){
            return self::response(self::page($data),$msg,$code);
        }
        return response(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    static function fail($msg = 'faild', $data = null, $code = 400)
    {
        return self::response($data,$msg,$code);
    }

    static function page(LengthAwarePaginator $data){
        return [
            'items'=>$data->items(),
            'current_page'=>$data->currentPage(),
            'last_page'=>$data->lastPage(),
            'per_page'=>$data->perPage(),
            'total'=>$data->total(),
        ];
    }

    static function response($data = null, $msg = 'success', $code = 200){
        return response(['code' => $code, 'msg' => $msg, 'data' => $data,'time'=>time()]);
    }
}
