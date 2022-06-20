<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateFormRequest extends Command
{

    protected $signature = 'request:generate {table}';


    protected $description = 'Generate request
                                {table} 数据表名';

    protected $basePath = 'app/Http/Requests';
    protected $table;
    protected $tableStruct;
    protected $namespace;
    protected $except = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $rules;
    protected $columns;

    public function handle()
    {
        $this->table = $this->argument('table');
        $this->getTableStructure();
        if ($this->tableStruct) {
            $this->namespace = Str::studly($this->table);
            list($rules, $columns) = $this->rules();
            $this->rules=$this->arrayToString($rules);
            $this->columns=$this->arrayToString($columns);
            $this->render('StoreRequest');
            $this->render('UpdateRequest');
            return;
        }

        $this->error("表{$this->table}不存在");
    }

    function getTableStructure()
    {
        $ret = DB::select("show tables like '{$this->table}'");
        if ($ret) {
            $this->tableStruct = DB::select("show full columns from {$this->table}");
        }

    }

    function arrayToString($arr){
        $str="[\n";
        foreach($arr as $k=>$item){
            $str .= "\t\t\t'{$k}' => '{$item}',\n";
        }
        return $str . "\t\t]";
    }

    protected function getType($type)
    {
        if ($this->isStartWith(['tinyint', 'smallint', 'mediumint', 'int', 'bigint'], $type)) {
            return 'int';
        }

        if ($this->isStartWith(['char', 'varchar', 'text'], $type)) {
            return 'string';
        }

        return null;
    }

    protected function isStartWith($array, $type)
    {
        $startWith = false;
        foreach ($array as $start) {
            if (Str::startsWith($type, $start)) {
                $startWith = true;
                break;
            }
        }
        return $startWith;
    }

    protected function getPath($file_name)
    {
        return base_path(rtrim($this->basePath, '/')) . '/' . Str::studly($this->table) . '/' . $file_name;
    }

    private function rules()
    {
        $rules = $columns = [];
        foreach ($this->tableStruct as $item) {

            if (in_array($item->Field, $this->except)) {
                continue;
            }
            $tmp_rules = [];
            // 不能为空
            if ($item->Null == 'NO') {
                $tmp_rules[] = 'required';
            }
            // 类型处理
            $type = $this->getType($item->Type);
            if ($type == 'int') {
                $tmp_rules[] = 'integer';
            } elseif ($type == 'string') {
                $tmp_rules[] = 'string';
                $tmp_rules[] = 'min:1';
                preg_match('/\d+/', $item->Type, $array);
                if ($array) {
                    $tmp_rules[] = 'max:' . $array[0];
                }
            }
            $rules[$item->Field] = implode('|', $tmp_rules);
            $columns[$item->Field] = $item->Comment;
        }
        return [$rules, $columns];
    }

    protected function isWrite($file_name)
    {
        return !(file_exists($file_name) && !$this->confirm('文件 [ ' . $file_name . ' ] 已经存在是否覆盖？'));
    }

    protected function render($className)
    {
        $file = $className . '.php';
        $file_name = $this->getPath($file);
        $dir_name = dirname($file_name);
        if (!file_exists($dir_name)) {
            @mkdir($dir_name, 0755, true);
        }

        if ($this->isWrite($file_name)) {
            $params=[
                'namespace'=>$this->namespace,
                'class_name'=>$className,
                'rules'=>$this->rules,
                'columns'=>$this->columns
            ];
            // 字符串替换
            $html   = $this->getRenderHtml();
            $search = array_map(function ($value) {
                return '{' . $value . '}';
            }, array_keys($params));

            $content = str_replace($search, array_values($params), $html);
            file_put_contents($file_name, $content);
            $this->info( "文件{$file_name}生成成功");
        } else {
            $this->info("文件{$file_name}已经存在，没有处理");
        }
    }

    public function getRenderHtml()
    {
        return <<<html
<?php

namespace App\Http\Requests\{namespace};

use Illuminate\Foundation\Http\FormRequest;

class {class_name} extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * 定义规则信息
     *
     * @return array
     */
    public function rules()
    {
        return {rules};
    }

    /**
     * 定义字段对应的名称
     *
     * @return array
     */
    public function attributes()
    {
        return {columns};
    }
}
html;

    }
}
