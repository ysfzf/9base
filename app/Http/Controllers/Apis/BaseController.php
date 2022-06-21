<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class BaseController extends Controller
{
    public function success($data = null, $msg = 'success', $code = 200)
    {
        if($data instanceof AnonymousResourceCollection  && $data->resource instanceof LengthAwarePaginator){

            return response([
                'code' => $code,
                'msg' => $msg,
                'data' => $this->page($data->resource)
            ]);
        }elseif($data instanceof LengthAwarePaginator){
            return response([
                'code' => $code,
                'msg' => $msg,
                'data' => $this->page($data)
            ]);
        }
        return response(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    public function fail($msg = 'faild', $data = null, $code = 400)
    {
        return response(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    public function clearToken(){
        JWTAuth::setToken(JWTAuth::getToken())->invalidate();
    }

    public function page(LengthAwarePaginator $data){
        return [
            'items'=>$data->items(),
            'current_page'=>$data->currentPage(),
            'last_page'=>$data->lastPage(),
            'per_page'=>$data->perPage(),
            'total'=>$data->total(),
        ];
    }

}
