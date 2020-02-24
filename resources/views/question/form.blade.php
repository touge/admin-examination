<div class="box box-default" id="questionEditor">
    <div class="box-header with-border">
        <h3 class="box-title"></h3>

        <div class="box-tools">
            <div class="btn-group pull-right" style="margin-right: 5px">
                <a href="{{route('exams.question.index')}}" class="btn btn-sm btn-default" title="{{__('admin.list')}}">
                    <i class="fa fa-list"></i> {{__('admin.list')}}
                </a>
            </div>
        </div>
    </div>

    <!-- form start -->
    <form ref="QuestionForm" method="post" accept-charset="UTF-8" pjax-container>
        <div class="box-body">

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{__('admin-examination::question.title')}}</label>
                        <input type="text" class="form-control" v-model="form.question" placeholder="">
                    </div>
                </div>
            </div>

            <div style="border-top: 1px solid #ccc;height: 1px;padding-bottom:10px;"></div>
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li v-for="(type, index) in types" v-bind:class="{active: form.type==index}" v-if="types">
                        <a href="javascript:;" v-on:click="setQuestionType(index)">
                            @{{type}}
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    {{--单选题--}}
                    @include("admin-examination::question.form.single_choice")
                    {{--复选题--}}
                    @include("admin-examination::question.form.multi_choices")
                    {{--判断题--}}
                    @include("admin-examination::question.form.true_false")
                    {{--填空题--}}
                    @include("admin-examination::question.form.fill_answer")
                    {{--问答题--}}
                    @include("admin-examination::question.form.text_answer")

                </div>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">{{__('admin-examination::question.analysis')}}</label>
                <input type="text" class="form-control" v-model="form.analysis" placeholder="">
            </div>
            <div style="border-top: 1px solid #ccc;height: 1px;padding-bottom:10px;"></div>


            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{__('admin-examination::question.tags')}}</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-bordered">
                        <tbody>

                        <tr v-for="(group,index) in group_tags" v-if="group_tags">
                            <td style="width: 120px;">
                                @{{ group.title }}
                            </td>
                            <td>
                                <span v-for="(tag, index) in group.tags">
                                    <input type="checkbox" v-bind:value="tag.id" data-tag  v-on:change="tagChange(index)" v-bind:checked="form.tags.indexOf(tag.id+'')!=-1">
                                    @{{ tag.title }}
                                </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
        </div>

        <!-- /.box-body -->

        <div class="box-footer">
            <button type="button" v-on:click="save" class="btn btn-success"> <i class="fa fa-save"></i> 保存</button>
        </div>
    </form>
</div>

<script type="text/javascript">
    !(function($,window,undefined){
        var question= {
            params: {},
            questionTypes: [],
            token: '',
            listUrl: '',
            form_attributes: {
                action_url: '',
                is_update: 0
            },
            vm: null,
        }
        question.formAttributes= function(attributes){
            this.form_attributes= attributes
            return this
        },
            question.parameters= function(params) {
                this.params= params
                return this
            }
        question.question_types= function(types){
            this.questionTypes= types
            return this
        }
        question.csrf_token= function(token){
            this.token= token
            return this
        }
        question.list_url= function(url){
            this.listUrl= url
            return this
        }

        /**
         * 向vue中传递数据
         */
        question.courses_selected= function(val){
            if(this.vue){
                this.vue.form.courses= val
            }
        },

            question.run= function(element){
                this.vue= new Vue({
                    el: element,
                    data: {
                        types: this.params.types,
                        options: {
                            single_choice_option: this.params.options.single_choice_option,
                            multi_choices_option: this.params.options.multi_choices_option,
                            true_false_option: this.params.options.true_false_option,
                            fill_answer: this.params.options.fill_answer,
                            text_answer: this.params.options.text_answer,
                        },
                        form: {
                            question: this.params.form.question,
                            alias: this.params.form.alias,
                            type: this.params.form.type,
                            analysis: this.params.form.analysis,
                            source: this.params.form.source,
                            options: this.params.form.options,
                            tags: this.params.form.tags,
                            courses: this.params.form.courses,
                        },
                        group_tags: this.params.group_tags,
                        courses: this.params.courses,
                        list_url: this.params.list_url,
                    },
                    created: function(){
                        // console.log(this.courses);
                    },
                    methods: {
                        setQuestionType:function (type)
                        {
                            this.form.type = type;
                        },
                        singleChoiceOptionChange: function(index, options){
                            if(options[index].is_answer)
                            {
                                options.forEach(function(o,i){
                                    if (i != index)
                                    {
                                        options[i].is_answer = false;
                                    }
                                })
                            }
                        },
                        addQuestionOptions: function(option, type) {
                            this.options[type].push(option)
                        },
                        removeQuestionOptions:function(index, type){
                            this.options[type].splice(index, 1)
                        },
                        tagChange:function (idx) {
                            var tags = [];
                            $('[data-tag]:checked').each(function(i,o){
                                tags.push($(o).val());
                            });
                            this.form.tags= tags
                        },
                        save: function(){
                            this.form.options= question.getFormOptions(this);
                            if(question.check(this)==false) return false;

                            Swal.fire({
                                title: '数据提交',
                                text: '正在保存数据，请稍候',
                                allowOutsideClick: false,
                                onBeforeOpen: function(){
                                    Swal.showLoading()
                                }
                            })

                            if(question.form_attributes.is_update==1){
                                this.form._method= 'put'
                            }
                            $.ajax({
                                url: question.form_attributes.action_url,
                                method: 'post',
                                data: this.form,
                                headers: {
                                    'X-CSRF-TOKEN': question.token
                                },
                                success: function(response){
                                    if(response.status=='successful')
                                    {
                                        Swal.close()
                                        $.pjax({ url: question.listUrl, container: '#pjax-container' });
                                    }
                                }
                            })
                        }
                    }
                })
            }
        question.check= function(that){
            var options= that.form.options

            var helper = {
                is_answer: function(){
                    for(var i=0; i<options.length; i++){
                        if(options[i].is_answer==true){
                            if(options[i].option==''){
                                return -2;
                            }
                            return true;
                        }
                    }
                    return -1;
                },
                answer: function(){
                    console.log(options)
                    for(var i=0; i<options.length; i++){
                        if(options[i].answer!=''){
                            return true;
                        }
                    }
                    return false;
                },
                option: function(){
                    for(var i=0;i< options.length; i++){
                        if(options[i].option != '')
                        {
                            return true;
                        }
                    }
                    return false;
                },
            };

            function checkIsAnswerOptionError(){
                var option_exists = helper.option()
                if(option_exists==false){
                    question.error('选项不能为空')
                    return true
                }

                var is_answer_exists= helper.is_answer();
                if(is_answer_exists==-1){
                    question.error('请选择答案')
                    return true;
                }
                if(is_answer_exists==-2){
                    question.error('答案未对应相应的选项')
                    return true;
                }
                return false
            }

            function checkAnswerError(){
                var answer_exists = helper.answer()

                if(answer_exists==false){
                    question.error('答案未对应相应的选项')
                    return true;
                }
                return false
            }

            if(that.form.question==''){
                question.error('题目不能为空')
                return false;
            }

            var error = false;
            switch (that.form.type+'') {
                case this.questionTypes.SINGLE_CHOICE:
                case this.questionTypes.MULTI_CHOICES:
                case this.questionTypes.TRUE_FALSE:
                    error= checkIsAnswerOptionError()
                    break;
                case this.questionTypes.FILL:
                case this.questionTypes.TEXT:
                    error= checkAnswerError()
                    break;
            }
            if(error==true) return false;

        }
        question.error= function(message){
            Swal.fire({
                html: "<strong>" + message + "</strong>",
                type: 'error',
                showConfirmButton: false,
                timer: 2000
            })
        }
        question.getFormOptions= function(that) {
            var options;

            var form_type= that.form.type + ''
            switch (form_type)
            {
                case this.questionTypes.SINGLE_CHOICE:
                    options= that.options.single_choice_option
                    break;
                case this.questionTypes.MULTI_CHOICES:
                    options= that.options.multi_choices_option
                    break;
                case this.questionTypes.TRUE_FALSE:
                    options= that.options.true_false_option
                    break;
                case this.questionTypes.FILL:
                    options= that.options.fill_answer
                    break;
                case this.questionTypes.TEXT:
                    options= that.options.text_answer
                    break;
            }
            return options;
        }
        window.question = question;
    })(jQuery,window);


    $(function(){
        var data= {
            types: <?php echo json_encode($data['types'], JSON_UNESCAPED_UNICODE);?>,
            options: {
                single_choice_option:<?php echo json_encode($data['single_choice_option'], JSON_UNESCAPED_UNICODE);?>,
                multi_choices_option:<?php echo json_encode($data['multi_choices_option'], JSON_UNESCAPED_UNICODE);?>,
                true_false_option:<?php echo json_encode($data['true_false_option'], JSON_UNESCAPED_UNICODE);?>,
                fill_answer:<?php echo json_encode($data['fill_answer'], JSON_UNESCAPED_UNICODE);?>,
                text_answer:<?php echo json_encode($data['text_answer'], JSON_UNESCAPED_UNICODE);?>,
            },
            form: {
                question: "<?php echo $data['form']['question']?:'';?>",
                alias: "<?php echo $data['form']['alias'];?>",
                type: <?php echo $data['form']['type']?>,
                analysis: "<?php echo $data['form']['analysis'];?>",
                source: "<?php echo $data['form']['source'];?>",
                options: [],
                tags: <?php echo json_encode($data['form']['tags'], JSON_UNESCAPED_UNICODE);?>,
            },
            group_tags: <?php echo json_encode($group_tags, JSON_UNESCAPED_UNICODE); ?>,
        }
        var QTCLASS={
            SINGLE_CHOICE: "{{$data["QTCLASS"]::SINGLE_CHOICE}}",
            MULTI_CHOICES: "{{$data["QTCLASS"]::MULTI_CHOICES}}",
            TRUE_FALSE: "{{$data["QTCLASS"]::TRUE_FALSE}}",
            FILL: "{{$data["QTCLASS"]::FILL}}",
            TEXT: "{{$data["QTCLASS"]::TEXT}}",
        }

        var action_url = "{{ $id ? route('exams.question.update', ['question'=>$id]) : route('exams.question.store') }}"
        var is_update= "{{ $id ? 1 : 0 }}"

        question.parameters(data)
            .formAttributes({action_url: action_url, is_update: is_update})
            .list_url("{{route('exams.question.index')}}")
            .question_types(QTCLASS)
            .csrf_token("{{csrf_token()}}")
            .run("#questionEditor")


        $(".courses").select2({"allowClear":true,"placeholder":{"id":"","text":"{{__('admin-examination::question.course')}}"}});
    })

</script>