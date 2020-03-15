<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-03-15
 * Time: 08:34
 */

namespace Touge\AdminExamination\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Touge\AdminExamination\Facades\PaperExam;
use Touge\AdminExamination\Http\Controllers\BaseController;
use Encore\Admin\Layout\Content;
use Touge\AdminExamination\Models\PaperExams;
use Touge\AdminOverwrite\Grid\Grid;
use Touge\AdminOverwrite\Grid\Displayers\Actions;

class CorrectionController extends BaseController
{
    /**
     * QuestionController constructor.
     */
    public function __construct()
    {
        $this->push_breadcrumb(
            [
                'text'=> trans("admin-examination::paper.correction.module-name"),
                'url'=> route('exams.correction.index')
            ]
        );
        parent::__construct();
    }

    public function index(Content $content){
        $this->set_breadcrumb($content);
        return $content
            ->header(__('admin-examination::paper.correction.module-name'))
            ->description(__('admin.list'))
            ->body($this->grid());
    }

    /**
     * @param $id
     * @param Content $content
     * @return Content
     */
    public function marking($id ,Content $content)
    {
        $this->push_breadcrumb(['text'=> __("admin-examination::paper.correction.marking")])
            ->set_breadcrumb($content);

        $paper_exam= PaperExam::fetch_one($id);

        $previous= $this->previous();
        $body= view('admin-examination::correction.marking', compact('paper_exam' ,'id', 'previous'));


        return $content
            ->header(__('admin-examination::paper.correction.module-name'))
            ->description(__('admin-examination::paper.correction.marking'))
            ->body($body);
    }



    /**
     * Get current resource route url.
     *
     * @param int $slice
     *
     * @return string
     */
    public function resource($slice = -2): string
    {
        $segments = explode('/', trim(\request()->getUri(), '/'));
        if ($slice !== 0) {
            $segments = array_slice($segments, 0, $slice);
        }
        return implode('/', $segments);
    }


    /**
     * Add field for store redirect url after update or store.
     *
     * @return void
     */
    protected function previous()
    {
        $previous = URL::previous();
        if (!$previous || $previous === URL::current()) {
            return;
        }

        if (Str::contains($previous, url($this->resource()))) {
            return $previous;
        }
    }

    /**
     * 批卷
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @see \Touge\AdminExamination\Http\Controllers\Api\CorrectionController::update
     */
    public function update($id, Request $request){
        $got_store= $request->get('got_store');

        /**
         * 每道试题的得分
         */
        $questions= [];
        foreach($got_store as $key=>$val){
            array_push($questions, ['id'=>$key, 'score'=>$val]);
        }


        $options= [
            'got_score'=> array_sum($got_store),
            'questions'=> $questions,
            'user'=> [
                'id'=> $this->user()->id,
                'name'=> $this->user()->name,
            ]
        ];
        PaperExam::update($id, $options);

        $resourcesPath = $this->resource(-2);
        $url = request('_previous_') ?: $resourcesPath;

        admin_toastr(trans('admin.save_succeeded'));
        return redirect($url);
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PaperExams());
        $modal= $grid->model();
        $grid->paginate(1);

        $modal->where(['customer_school_id'=> $this->customer_school_id()])
            ->orderBy('id', 'DESC');

        $grid->id('ID');
        $grid->column('paper.title', __('admin-examination::paper.title'));
        $grid->column('user_name', __('admin-examination::paper.correction.user'));
        $grid->column('is_judge', __('admin-examination::paper.correction.is_judge'));

        $grid->column('paper.time_limit_value' ,__('admin-examination::paper.time-limit-enable'))->states();
        $grid->column('paper.pass_score', __('admin-examination::paper.pass-score'));
        $grid->column('paper.total_score', __('admin-examination::paper.total-score'));

        $grid->disableRowSelector()
            ->disableCreateButton()
            ->disableColumnSelector()
            ->disableFilter()
            ->disableExport();
        $grid->actions(function(Actions $actions){
            $actions->disableView()->disableEdit();

            $marking_url= admin_url("examination/correction/{$actions->getKey()}/marking");
            $actions->prepend((new Actions\CustomActionButton([
                'url'=> $marking_url,
                'title'=> __('admin-examination::paper.correction.marking'),
                'icon'=> 'fa-edit',
                'id'=>$actions->getKey(),
                'class'=>'btn-success btn-member-management'
            ]))->render());

        });

        return $grid;
    }
}