<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GridModalForm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:gridform {name} {--path=Admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '给表格创建弹窗表单';


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name=$this->argument('name');
        $path=$this->option('path');
        if(empty($path)) $path='Admin';

        $form=$this->createForm($name,$path);
        $this->info($form);
        if($form){
            $this->createAction($form,$name,$path);
        }
        $this->info('done!');
    }

    protected function createForm($name,$path){
        $className=$name.'Form';
        $namespace=$path.'/Forms';
        $dir=app_path($namespace);
        if(!is_dir($dir)){
            @mkdir($dir, 0755, true);
        }
        $fileName=$dir.'/'.$className.'.php';
        $params=[
            'path'=>$path,
            'className'=>$className,
        ];
        $search = array_map(function ($value) {
            return '{' . $value . '}';
        }, array_keys($params));

        $content = str_replace($search, array_values($params), $this->getForm());
        if($this->isWrite($fileName)){
            file_put_contents($fileName,$content);
            $this->info( "文件{$fileName}生成成功");

        }
        $namespace=str_replace('/','\\',$namespace);
        return $namespace.'\\'.$className;
    }

    protected function isWrite($file_name)
    {
        return !(file_exists($file_name) && !$this->confirm('文件 [ ' . $file_name . ' ] 已经存在是否覆盖？'));
    }

    protected function createAction($form,$name,$path){
        $className=$name.'Action';
        $namespace=$path.'/'.'Actions/Grid';
        $dir=app_path($namespace);
        if(!is_dir($dir)){
            @mkdir($dir, 0755, true);
        }
        $fileName=$dir.'/'.$className.'.php';
        $params=[
            'path'=>$path,
            'className'=>$className,
            'form'=>$form
        ];
        $search = array_map(function ($value) {
            return '{' . $value . '}';
        }, array_keys($params));

        $content = str_replace($search, array_values($params), $this->getAction());
        if($this->isWrite($fileName)){
            file_put_contents($fileName,$content);
            $this->info( "文件{$fileName}生成成功");
        }
    }

    protected function getForm(){
        return <<<STR
<?php

namespace App\{path}\Forms;

use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Symfony\Component\HttpFoundation\Response;

class {className} extends Form implements LazyRenderable
{
    use LazyWidget;

    public function handle(array \$input)
    {
         // \$this->payload['id'];
        //return \$this->error('当前用户没有绑定门店');
        //return \$this->success('设置成功了');
    }


    public function form()
    {
        // \$this->payload['id'];
        \$this->text('name');
    }
}

STR;

    }


    protected function getAction(){
        return <<<STR
<?php

namespace App\{path}\Actions\Grid;

use App\{form} as Form;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Dcat\Admin\Widgets\Modal;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class {className} extends RowAction
{
	protected \$title = '菜单';

    function render()
    {
         return Modal::make()
             ->lg()
             ->title(\$this->title)
             ->body(Form::make()->payload(['id'=>\$this->getKey()]))
             ->button(\$this->title);
    }
}
STR;

    }
}
