<div class="box-body" v-for="(question, index) in paper_questions">
    <table class="table">
        <tr>
            <td colspan="2">
                <div class="question-alert">
                    <span style="float: right;">
                        <a href="javascript:;" title="向上移动" v-show="index>0" v-on:click="questionMoveUp(index)"><i class="fa fa-arrow-up"></i></a>&nbsp;&nbsp;
                        <a href="javascript:;" title="向下移动" v-show="index+1<paper_questions.length" v-on:click="questionMoveDown(index)"><i class="fa fa-arrow-down"></i></a>&nbsp;&nbsp;
                        <a href="javascript:;" title="移动到指定位置" v-on:click="questionMoveTo(index)"><i class="fa fa-crosshairs"></i></a>&nbsp;&nbsp;
                        {{--<a target="_blank"><i class="fa fa-edit"></i></a>&nbsp;--}}
                        <a href="javascript:;" v-on:click="questionDelete(index)"><i class="fa fa-trash"></i></a>
                    </span>
                    <span>第 @{{ index + 1 }} 题</span>
                </div>

            </td>
        </tr>
        <tr>
            <td>
                <div style="border:1px solid #CCC;padding:10px;border-radius:3px;line-height:2em;">
                    <div style="padding:10px 0;">
                        <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;">
                            @{{ question.type_text }}
                        </div>

                        <div style="padding:10px 0;">
                            <div>
                                <p>@{{ question.question }}</p>
                            </div>

                            {{--试题选项--}}
                            <div v-if="question.items">
                                <div style="padding:0 0 0 20px;" v-if="question.items.options" v-for="(option, index) in question.items.options">
                                    <div style="float:left;margin:0 0 0 -20px;">@{{ option.key_ord }}.</div>
                                    <p>@{{ option.option }}</p>
                                </div>
                            </div>

                            {{--试题答案--}}
                            <div v-if="typeof question.items.answers=='string'">
                                <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;margin:10px 0 0 0;">
                                    答案：@{{ question.items.answers }}
                                </div>
                            </div>
                            <div v-else-if="typeof question.items.answers == 'object'">
                                <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;margin:10px 0 0 0;">
                                    答案
                                </div>
                                <div style="padding:10px 0;" v-if="question.items">
                                    <div style="padding:0 0 0 20px;" v-if="question.items.answers" v-for="(answer, index) in question.items.answers">
                                        <div style="float:left;margin:0 0 0 -20px;"> @{{ index + 1 }}.</div>
                                        <p>@{{ answer.answer }}.</p>
                                    </div>
                                </div>
                            </div>

                            {{--试题解析--}}
                            <div style="padding:10px 0;" v-if="question.analyses">
                                <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;">
                                    解析
                                </div>
                                <div style="padding:10px 0;">
                                    @{{ question.analyses.analysis }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </td>
            <td width="150">
                <div class="input-group">
                    <div class="input-group-addon">
                        分值
                    </div>
                    <input type="text" class="form-control" v-model="question.score">
                </div>
            </td>
        </tr>
    </table>
</div>