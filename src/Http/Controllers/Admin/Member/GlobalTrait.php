<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-02
 * Time: 17:47
 */

namespace Touge\AdminExamination\Http\Controllers\Admin\Member;

use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form\Tools;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\Route;
use Touge\AdminExamination\Models\Member;
use Touge\AdminOverwrite\Form;
use Touge\AdminOverwrite\Grid\Displayers\Actions;
use Touge\AdminOverwrite\Grid\Grid;
use Illuminate\Support\Str;

trait GlobalTrait
{
    use HasResourceActions;
    /**
     * 身份，0：学生 1：老师
     * @var int
     */
    protected $identity= 0;

    /**
     * @var array|string|null
     */
    protected $moduleName;

    /**
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $this->set_breadcrumb($content);

        return $content
            ->header($this->moduleName)
            ->description(__('admin.list'))
            ->body($this->grid());
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        $this->push_breadcrumb(['text'=> __("admin.create")])
            ->set_breadcrumb($content);
        return $content
            ->header($this->moduleName)
            ->description(__('admin.create'))
            ->body($this->form());
    }


    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        $this->push_breadcrumb(['text'=> trans("admin.edit")])
            ->set_breadcrumb($content);
        return $content
            ->header($this->moduleName)
            ->description(__('admin.edit'))
            ->body($this->form()->edit($id));
    }

    /**
     * @return array
     */
    protected function gender(){
        return [
          1=> __('admin-examination::member.man'),
          2=> __('admin-examination::member.woman')
        ];
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function getModel(){
        return new Member();
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid($this->getModel());

        $model= $grid->model();
        $model->where(['identity'=>$this->identity ]);
        $model->orderBy('id', 'DESC');


        $grid->id('Id');
        $grid->name(__('admin-examination::member.name'));
        $grid->email(__('admin-examination::member.email'));
        $grid->mobile(__('admin-examination::member.mobile'));


        $grid->disableFilter()
            ->disableColumnSelector()
            ->disableExport()
            ->disableRowSelector();
        $grid->actions(function (Actions $actions){
            $actions->disableView();
        });


        return $grid;
    }


    /**
     * 判断当前表单是否为创建
     *
     * @return bool
     */
    protected function formIsCreate(){
        return Str::contains(Route::currentRouteName(), 'create');
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form($this->getModel());

        $form->text('name', __('admin-examination::member.name'))->rules('required');
        $form->radio("gender",__("admin-examination::member.gender"))->options(
            $this->gender()
        )->default('1');
        $form->hidden("identity")->value($this->identity);

        $form->email('email', trans('admin-examination::member.email'))->rules(function(Form $form){
            $table = $form->model()->getTable();
            if(!$id = $form->model()->id){
                return "required|unique:{$table},email";
            }
            return "required|unique:{$table},email,{$id}";
        })->help(__('admin-examination::member.help.input-true-email'));

        $form->mobile('mobile', trans('admin-examination::member.mobile'))->rules(function(Form $form){
            $table = $form->model()->getTable();
            if(!$id = $form->model()->id){
                return "required|unique:{$table},mobile";
            }
            return "required|unique:{$table},mobile,{$id}";
        });


        $form->password('password', trans('admin.password'))
            ->attribute(['autocomplete'=> 'new-password'])
            ->rules('required');


        $form->datetime("expire_time", __('admin-examination::member.expire-time'))
            ->format('YYYY-MM-DD HH:mm:ss')
            ->help( __('admin-examination::member.help.no-select-no-expire') );

        $form->text("mark" ,__('admin-examination::member.mark'));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        $form->tools(function(Tools $tools){
            $tools->disableView()
                ->disableDelete();
        });
        $form->disableCreatingCheck()
            ->disableEditingCheck()
            ->disableViewCheck();
        return $form;
    }
}