<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-01
 * Time: 13:32
 */

namespace Touge\AdminExamination\Http\Controllers\Api;

use Touge\AdminExamination\Http\Controllers\BaseApiController;
use Touge\AdminExamination\Types\GradationType;

class GradationController extends BaseApiController
{
    public function index(){
        $gradations= GradationType::gradations();
        $gradation= GradationType::gradation(config('touge-admin-examination.gradation'),'slug');


        $data= ['gradations'=>$gradations, 'current'=>$gradation];
        return $this->success($data);
    }
}