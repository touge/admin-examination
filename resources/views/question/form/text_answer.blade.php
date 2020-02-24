<div class="tab-pane" v-bind:class="{ active: form.type == {{$data["QTCLASS"]::TEXT}} }"  v-show="form.type == {{$data["QTCLASS"]::TEXT}}">
    <table class="table table-bordered">
        <tr>
            <td>答案</td>
        </tr>
        <tr>
            <td>
                <textarea class="form-control" style="height: 250px;" v-model="options.text_answer[0].answer"></textarea>
            </td>
        </tr>
    </table>
</div>
