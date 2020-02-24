<div class="tab-pane" v-bind:class="{ active: form.type == {{$data["QTCLASS"]::TRUE_FALSE}} }"  v-show="form.type == {{$data["QTCLASS"]::TRUE_FALSE}}">
    <table class="table table-bordered">
        <tr>
            <td width="100" class="text-center">是否答案</td>
            <td>选项</td>
        </tr>
        <tr v-for="(opt,idx) in options.true_false_option">
            <td class="text-center">
                <label>
                    <input type="checkbox" name="option" v-model="opt.is_answer" v-on:change="singleChoiceOptionChange(idx, options.true_false_option)" value="true" />
                </label>
            </td>
            <td>
                <div v-html="opt.option"></div>
            </td>
        </tr>
    </table>
</div>
