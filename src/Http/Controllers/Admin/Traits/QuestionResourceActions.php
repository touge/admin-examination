<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-19
 * Time: 19:35
 */

namespace Touge\AdminExamination\Http\Controllers\Admin\Traits;

use Encore\Admin\Form;
use Illuminate\Http\Request;
use Touge\AdminExamination\Supports\TagHelper;

use Touge\AdminExamination\Models\Question as QuestionModel;
use Touge\AdminExamination\Facades\Question;
trait QuestionResourceActions
{
    /**
     * 提交数据收集整理: question 数据
     * @param Request $request
     * @return array
     */
    protected function get_inputs(Request $request)
    {
        $tags= $request->get('tags');
        $options= [
            'alias'=> $request->get('alias'),
            'question'=> $request->get('question'),
            'type'=> $request->get('type'),
            'tags'=> $tags ? TagHelper::array2String($request->get('tags')) : '',
            'options'=> $request->get('options'),
            'analysis'=> $request->get('analysis'),
            'courses'=> $request->get('courses'),
        ];
        return $options;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $question= Question::store($this->get_inputs($request));

        if(!$question){
            return response()->json([
                'status'=> 'failed',
                'message'=> '数据录入失败',
            ]);
        }
        return response()->json([
            'status'=> 'successful',
            'question'=> $question->toArray()
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $question= Question::update($this->get_inputs($request) ,$id);

        if(!$question){
            return response()->json([
                'status'=> 'failed',
                'message'=> '数据录入失败',
            ]);
        }
        return response()->json([
            'status'=> 'successful',
            'question'=> $question->toArray()
        ]);
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $form= new Form(new QuestionModel);
        if ($form->destroy($id)) {
            $data = [
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ];
        } else {
            $data = [
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ];
        }

        return response()->json($data);
    }
}