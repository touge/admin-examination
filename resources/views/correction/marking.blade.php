<div id="paperEditor">
    @include('admin-examination::correction.marking-top-box')
    <form action="{{route('exams.correction.update', ['correction'=> $id])}}" method="post">
        <input type="hidden" name="_token" value="{{csrf_token()}}">
        <input type="hidden" name="_previous_" value="{{$previous}}">
        <div class="box box-default">
            <div class="box-body">
                <div>
                    <label>{{__('admin-examination::paper.question-list')}}</label>
                </div>
                <div class="box box-default">
                    @include('admin-examination::correction.marking-questions')
                </div>

            </div>

            <div class="box-footer">
                <div class="pull-left">
                <button type="submit" class="btn btn-success"> <i class="fa fa-save"></i> 保存</button>
                </div>
            </div>
        </div>
    </form>
</div>


<script type="text/javascript">
    $(function(){
        $('.locked.la_checkbox').bootstrapSwitch({
            size:'small',
            onText: 'ON',
            offText: 'OFF',
            onColor: 'success',
            offColor: 'default'
        });
    })

</script>