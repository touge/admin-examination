<div class="tab-pane" v-bind:class="{ active: form.type == {{$data["QTCLASS"]::SINGLE_CHOICE}} }"  v-show="form.type == {{$data["QTCLASS"]::SINGLE_CHOICE}}">
    <table class="table table-bordered">
        <tbody>
        <tr>
            <td width="100" class="text-center">是否答案</td>
            <td>选项</td>
            <td width="30"></td>
        </tr>
        <tr v-for="(opt,idx) in options.single_choice_option">
            <td class="text-center">
                <label>
                    <input type="checkbox" name="option" v-model="opt.is_answer" v-on:change="singleChoiceOptionChange(idx, options.single_choice_option)" value="true" />
                </label>
            </td>
            <td class="text-center">
                <input type="text" class="form-control" v-model="opt.option"/>
            </td>
            <td>
                <a href="javascript:;" v-on:click="removeQuestionOptions(idx, 'single_choice_option')"><i class="fa fa-remove"></i></a>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="text-center">
                <a href="javascript:;" v-on:click="addQuestionOptions({is_answer:false,option:''}, 'single_choice_option')"><i class="fa fa-plus"></i> 增加一个选项</a>
            </td>
        </tr>
        </tbody>
    </table>
</div>
