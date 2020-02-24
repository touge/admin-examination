<div class="tab-pane" v-bind:class="{ active: form.type == {{$data["QTCLASS"]::FILL}} }"  v-show="form.type == {{$data["QTCLASS"]::FILL}}">
    <table class="table table-bordered">
        <tr>
            <td width="5">&nbsp;</td>
            <td>答案</td>
            <td width="5"></td>
        </tr>
        <tr v-for="(opt,idx) in options.fill_answer">
            <td>@{{idx+1}}</td>
            <td>
                <input type="text" class="form-control" v-model="opt.answer"/>
            </td>
            <td>
                <a href="javascript:;" v-on:click="removeQuestionOptions(idx, 'fill_answer')"><i class="fa fa-remove"></i></a>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="text-center">
                <a href="javascript:;" v-on:click="addQuestionOptions({answer:''}, 'fill_answer')"><i class="fa fa-plus"></i> 增加一个选项</a>
            </td>
        </tr>
    </table>
</div>
