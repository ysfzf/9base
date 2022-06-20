<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateModels extends Command
{
    protected $signature = 'model:generate {tables?} {--namespace=App\\Models} {--dir=app/Models} {--deleted=deleted_at} { --f|force}';
    protected $description = 'Generate models
                            model:generate users,goods,orders
    ';
    protected $namespace;
    protected $delted_at;
    protected $dir;

    protected $force;
    function handle(){
        $tables=$this->getTables();
        if(!$tables){
            return;
        }
        $this->info('将会为以下表生成Model文件:'.PHP_EOL);
        $this->info(implode(' ',$tables));
        if(!$this->confirm("是否确定继续?")){
            return ;
        }

        $this->namespace=$this->option('namespace');
        $this->delted_at=$this->option('deleted');
        $this->dir=$this->option('dir');
        $this->force=$this->option('force');

        if(!$this->namespace){
            $this->namespace='App\\Models';
        }

        if(!$this->dir){
            $this->dir='app/Models';
        }

        if(!$this->delted_at){
            $this->delted_at='deleted_at';
        }

        $param=[
            'namespace'=>$this->namespace,
            'basemodel'=>$this->generateBaseModel(),
            'hidden'=>'',
            'softdelete'=>'',
            'classname'=>'',
            'table'=>''
        ];
        foreach ($tables as $table){
            if($this->isSoftDelete($table)){
                $param['hidden']="protected \$hidden=['{$this->delted_at}'];";
                $param['softdelete']='use SoftDeletes;';
            }
            $param['table']=$table;
            $param['classname']=Str::studly($table).'Model';
            $fileName=base_path(rtrim($this->dir, '/')) . "/{$param['classname']}.php";
            $this->writeFile($fileName,$this->modelHtlm(),$param);
        }
    }

    protected function isSoftDelete($tableName){
        $ret=DB::select("show columns from `{$tableName}` where `Field`='{$this->delted_at}' ");
        return !empty($ret);
    }

    //获取需要生成的表，不存在的表将会过虑
    protected function getTables(){
        $tables=$this->argument('tables');
        $all=$this->getAllTables();
        if(!$tables){
            return $all;
        }
        $result=[];
        $tables=explode(',',$tables);
        foreach($tables as $table){
            if(in_array($table,$all)){
                $result[]=$table;
            }
        }

        return $result;
    }

    protected function getAllTables(){
        $tables=DB::select('show tables');
        $result=[];
        foreach($tables as $table){
            $tmp=array_values(collect($table)->toArray());
            if(isset($tmp[0])){
                $result[]=$tmp[0];
            }
        }
        return $result;
    }

    protected function modelHtlm(){
        return <<<HTML
<?php

namespace {namespace};

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use {basemodel} as Base;
class {classname} extends Base
{
    {softdelete}
    
    protected \$table = '{table}';
    {hidden}

}
HTML;

    }

    protected function generateBaseModel(){
        $html=<<<HTML
<?php

namespace {namespace};

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class {classname} extends Model
{

    protected \$guarded=['id'];

}
HTML;
        $className='BaseModel';
        $fileName=base_path(rtrim($this->dir, '/')) . "/{$className}.php";
        $this->writeFile($fileName,$html,[
            'namespace'=>$this->namespace,
            'classname'=>$className,
        ]);

        return $this->namespace.'\\'.$className;
    }


    protected function writeFile($fileName,$html,$param){

        if(!$this->force && file_exists($fileName)){
            //不重写，并且文件已经存在
            $this->info("{$fileName}已经存在,没有处理");
            return ;
        }
        $search = array_map(function ($value) {
            return '{' . $value . '}';
        }, array_keys($param));

        $content = str_replace($search, array_values($param), $html);
        file_put_contents($fileName, $content);
        $this->info( "文件{$fileName}生成成功");
    }
}
