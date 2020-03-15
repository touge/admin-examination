<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"></h3>

        <div class="box-tools">
            <div class="btn-group pull-right" style="margin-right: 5px">
                <a href="{{ $previous ? :route('exams.correction.index')}}" class="btn btn-sm btn-default" title="{{__('admin.list')}}">
                    <i class="fa fa-list"></i> {{__('admin.list')}}
                </a>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{__('admin-examination::paper.title')}}</label>
                    <input type="text" class="form-control" value="{{$paper_exam['paper_title']}}" disabled="disabled">
                </div>
            </div>


            <div class="col-md-6">
                <div class="form-group">
                    <label>{{__('admin-examination::paper.categories.module-name')}}</label>
                    <input type="text" class="form-control" value="{{$paper_exam['paper_category']}}" disabled="disabled">
                </div>
                <input type="hidden" v-model="form.customer_school_id">
            </div>
        </div>
    </div>
</div>
<div class="box box-default">
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <label>分数设置</label>
                        <div class="input-group">
                            <span class="input-group-addon">总分</span>
                            <input type="text" class="form-control name" value="{{$paper_exam['paper_total_score']}}" disabled>
                            <span class="input-group-addon">分</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>&nbsp;</label>
                        <div class="input-group">
                            <span class="input-group-addon">及格分</span>
                            <input type="text" class="form-control name" value="{{$paper_exam['paper_pass_score']}}" >
                            <span class="input-group-addon">分</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <label>答题时间限制</label>
                        <div class="input-group">
                            <input type="checkbox" class="form-control locked la_checkbox" disabled {{ $paper_exam['paper_time_limit_enable']==1 ?'checked' :'' }}/>
                        </div>
                    </div>
                    <div class="col-md-8" v-show="form.time_limit_enable==1">
                        <label>&nbsp;</label>
                        <div class="input-group">
                            <span class="input-group-addon">限制时间</span>
                            <input type="text" id="name" name="name" value="{{$paper_exam['paper_time_limit_value']}}" class="form-control name" disabled>
                            <span class="input-group-addon">分</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>