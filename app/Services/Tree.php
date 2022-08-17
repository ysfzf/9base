<?php
namespace App\Services;

class Tree
{
    protected $pid;
    protected $id;

    function __construct($pid='pid',$id='id')
    {
        $this->pid=$pid;
        $this->id=$id;
    }

    function make(array $arr):array{
        $items=[];
        foreach($arr as $item){
            $items[$item[$this->id]]=$item;
        }
        $tree = [];
        foreach ($items as $item)
            if (isset($items[$item[$this->pid]])){
                $items[$item[$this->pid]]['children'][] = &$items[$item[$this->id]];
            }else{
                $tree[] = &$items[$item[$this->id]];
            }
        return $tree;
    }

}
