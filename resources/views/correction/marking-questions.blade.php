@foreach($paper_exam['questions'] as $key=>$question)
    <div class="box-body" v-for="(question, index) in paper_questions">
        <table class="table">
            <tr>
                <td colspan="2">
                    <div class="question-alert">
                        <span>第 {{$key + 1}} 题</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="border:1px solid #CCC;padding:10px 10px 0 10px;border-radius:3px;line-height:2em;">
                        <div style="padding:10px 0;">
                            <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;">
                                {{ \Touge\AdminExamination\Types\QuestionType::text($question['type']) }}
                            </div>

                            <div style="padding:10px 0;">
                                <div>
                                    <p style="color:blue"> {{$question['title']}}</p>
                                </div>


                                @if(in_array($question['type'], [1,2]))
                                    <div>
                                        @foreach($question['options'] as $key=>$options)
                                            <div style="padding:0 0 0 20px;">
                                                <div style="float:left;margin:0 0 0 -20px;">{{chr(ord('A') + $key) }}. </div>
                                                <p>{{ $options['option'] }}</p>
                                            </div>
                                        @endforeach
                                        <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;margin:10px 0 0 0;">
                                            答案：@foreach($question['answer'] as $key=>$answer) <span style="color: red;">{{chr(ord('A') + $answer)}}</span> @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($question['type']==3)
                                <div>
                                    @foreach($question['options'] as $key=>$options)
                                    <div style="padding:0 0 0 20px;">
                                        <div style="float:left;margin:0 0 0 -20px;">{{chr(ord('A') + $key) }}. </div>
                                        <p>{{ $options['option'] }}</p>
                                    </div>
                                    @endforeach
                                    <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;margin:10px 0 0 0;">
                                        答案：<span style="color: red;">{{chr(ord('A') + $question['answer'][0]) }}</span>
                                    </div>
                                </div>
                                @endif

                                @if($question['type']==4)
                                    <div>
                                        <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;margin:10px 0 0 0;">
                                            答案
                                        </div>
                                        <div style="padding:10px 0;">
                                            @foreach($question['answers'] as $key=>$answers)
                                            <div style="padding:0 0 0 20px;">
                                                <div style="float:left;margin:0 0 0 -20px;"> 空{{ $key + 1 }}. </div>
                                                <p><span style="color: red;">{{ $answers['answer'] }}</span></p>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($question['type']==5)
                                    <div>
                                        <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;margin:10px 0 0 0;">答案</div>
                                        <div style="padding:10px 0;">
                                            @foreach($question['answers'] as $key=>$answers)
                                                <div style="padding:0 0 0 0px;">
                                                    <p><span style="color: red;">{{ $answers['answer'] }}</span></p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div style="padding-top: 10px;" v-if="question.analyses">
                                    <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;">
                                        解析
                                    </div>
                                    <div style="padding:10px 0;">
                                        {{ $question['analyses']['analysis'] }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </td>

                <td width="300">
                    <div class="input-group" style="margin-bottom:5px;">
                        <div class="input-group-addon">题目分值</div>
                        <input type="text" class="form-control" value="{{$question['score']}}" disabled>
                    </div>
                    <div class="input-group" style="margin-bottom:5px;">
                        <div class="input-group-addon">考生得分</div>

                        <input type="text" style="color:red" name="got_store[{{$question['id']}}]" class="form-control" value="{{$question['paper_exam']['score']}}">
                    </div>
                    <div class="input-group" style="margin-bottom:5px;">
                        @if(in_array($question['type'], [1,2]))
                            <div class="input-group-addon">考生答案</div>
                            <input type="text" style="color: blue;" class="form-control" value="{{format_single_choice_view($question['paper_exam']['answer'])}}" disabled>
                        @endif
                        @if($question['type']==3)
                            <div class="input-group-addon">考生答案</div>
                            <input type="text" style="color: blue;" class="form-control" value="{{format_true_false_view($question['paper_exam']['answer'])}}" disabled>
                        @endif
                        @if($question['type']==4)
                            <div class="input-group-addon">考生答案</div>
                            @foreach($question['paper_exam']['answer'] as $answer)
                                <input type="text" style="color: blue;" class="form-control" value="{{format_fill_view($answer)==null ?'无答案' :$answer}}" disabled>
                            @endforeach
                        @endif
                        @if($question['type']==5)
                            <div class="input-group">考生答案</div>
                                <textarea style="color: blue;width: 300px;height:150px;" class="form-control" value="" disabled>{{format_text_view($question['paper_exam']['answer'])}}</textarea>
                        @endif
                    </div>

                </td>
            </tr>
        </table>
    </div>
@endforeach