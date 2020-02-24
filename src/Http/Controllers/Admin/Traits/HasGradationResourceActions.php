<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-08
 * Time: 15:52
 */

namespace Touge\AdminExamination\Http\Controllers\Admin\Traits;


use Touge\AdminExamination\Types\GradationType;

trait HasGradationResourceActions
{
    /**
     * @param $gradation
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store($gradation)
    {
        return $this->form($gradation)->store();
    }

    /**
     * @param $gradation
     * @param $id
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|mixed|\Symfony\Component\HttpFoundation\Response|null
     */
    public function update($gradation, $id)
    {
        return $this->form($gradation)->update($id);
    }

    /**
     * @param $gradation
     * @param $id
     * @return mixed
     */
    public function destroy($gradation, $id)
    {
        return $this->form($gradation)->destroy($id);
    }


    /**
     *
     * 用于select或者listbox中的阶段列表
     *
     * @return array
     */
    protected function gradation_options(): array
    {
        $gradation_options= [];
        foreach(GradationType::getList() as $key=>$item){
            $gradation_options[$key]= $item['text'];
        }
        return $gradation_options;
    }


}