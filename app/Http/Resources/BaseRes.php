<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseRes extends JsonResource
{
    protected $visibles;
    protected function setVisible($columns){
        $result=[];
        foreach($columns as $column){
            if($column=='created_at' || $column=='updated_at'){
                $result[$column]=(string)$this->{$column};
            }else{
                $result[$column]=$this->{$column};
            }

        }

        return $result;
    }

    function toArray($request)
    {
        if($this->visibles){
            return $this->setVisible($this->visibles);
        }
        return [];
    }
}
