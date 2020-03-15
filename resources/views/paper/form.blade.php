
<div id="paperEditor">

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"></h3>

            <div class="box-tools">
                <div class="btn-group pull-right" style="margin-right: 5px">
                    <a href="{{route('exams.paper.index')}}" class="btn btn-sm btn-default" title="{{__('admin.list')}}">
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
                        <input type="text" v-model="form.title" class="form-control" placeholder="{{__('admin-examination::paper.title')}}">
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{__('admin-examination::paper.categories.module-name')}}</label>
                        <select v-select2="" class="form-control paper-categories" v-model="form.category_id">
                            <option v-for="(category, index) in categories" v-if="categories" v-bind:value="category.id">@{{ category.name }}</option>
                        </select>
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
                                <input type="text" class="form-control name" placeholder="输入总分" v-model="form.total_score">
                                <span class="input-group-addon">分</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>&nbsp;</label>
                            <div class="input-group">
                                <span class="input-group-addon">及格分</span>
                                <input type="text" class="form-control name" placeholder="输入及格分" v-model="form.pass_score">
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
                                <input type="checkbox" class="form-control locked la_checkbox" v-model="form.time_limit_enable" />
                                <input type="hidden" class="locked" name="time_limit_enable"/>
                            </div>
                        </div>
                        <div class="col-md-8" v-show="form.time_limit_enable==1">
                            <label>&nbsp;</label>
                            <div class="input-group">
                                <span class="input-group-addon">限制时间</span>
                                <input type="text" id="name" name="name" v-model="form.time_limit_value" class="form-control name" placeholder="输入时间">
                                <span class="input-group-addon">分</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-default">
        <div class="box-body">
            <div>
                <label>{{__('admin-examination::paper.question-list')}}</label>
            </div>

            <div class="box box-default" v-if="paper_questions.length>0">
                @include('admin-examination::paper.questions')
            </div>

            <div style="margin:10px 0;"><a href="javascript:;" onclick="paper.question_modal()"><i class="fa fa-plus"></i> 添加试题</a> </div>

        </div>

        <div class="box-footer">
            <button v-on:click="save" type="button" class="btn btn-success"> <i class="fa fa-save"></i> 保存</button>
        </div>
    </div>

</div>

<script type="text/javascript">
    !(function($,window,undefined){
        var paper= {
            vue: null,
            params: [],
            _token: null,
            urls: {
                paper_list: null,
                question_search: null,
                question_preview: null,
            },
            form_attributes: {
                action_url: '',
                is_update: 0
            },
            //弹窗的MODAL ID
            select_question_modal_id: null,
        };
        paper.parameters= function(params){
            this.params= params
            return this
        }
        paper.csrf_token= function(token){
            this._token= token
            return this
        }
        paper.formAttributes= function(attributes){
            this.form_attributes= attributes
            return this
        }
        paper.set_urls= function(urls){
            this.urls= urls
            return this
        }
        paper.question_modal= function(){
            this.select_question_modal_id= 'paper-question-modal-' + utils.uuid(12);
            utils.modal({
                style: 'default',
                title: '试题选择',
                size: 'modal-lg',
                modal_id: this.select_question_modal_id,
                url: this.urls.question_search,
            })
        }
        paper.vueFormAttribute= function(name, value){
            if(this.vue){
                this.vue.form[name]= value
            }
        },
        /**
         * 接收弹窗回传数据
         */
        paper.receive_question_modal_data= function(ids)
        {
            if(this.vue){
                this.vue.get_questions(ids, function(){
                    $("#" + paper.select_question_modal_id).modal('hide')
                })
            }
        }
        paper.run= function(){
            this.vue= new Vue({
                el: "#paperEditor",
                data: {
                    categories: this.params.categories,
                    gradations: this.params.gradations,
                    form: {
                        id: this.params.form.id,
                        category_id: this.params.form.category_id,
                        customer_school_id: this.params.form.customer_school_id,
                        alias: this.params.form.alias,
                        title: this.params.form.title,
                        is_public: this.params.form.is_public,
                        time_limit_enable: this.params.form.time_limit_enable,
                        time_limit_value: this.params.form.time_limit_value,
                        pass_score: this.params.form.pass_score,
                        total_score: this.params.form.total_score,
                    },
                    paper_questions: this.params.paper_questions
                },
                mounted: function () {
                    var me = this;
                    @if(!empty($data['paper_questions']))
                        var ids = [];
                        @foreach($data['paper_questions'] as $paper_question)
                            ids.push({{$paper_question['question_id']}});
                        @endforeach

                        this.get_questions(ids,function (response) {
                            // console.log(response)
                            @foreach($data['paper_questions'] as $index=>$paper_question)
                                me.paper_questions["{{$index}}"].score= "{{$paper_question['score']}}"
                            @endforeach
                        });

                    @endif
                },
                methods: {
                    get_questions: function(ids, callback){
                        var self= this;
                        $.ajax({
                            url: paper.urls.question_preview,
                            method: 'post',
                            headers: {
                                'X-CSRF-TOKEN': paper._token
                            },
                            data: {ids: ids},
                            success: function(response){
                                for(var i=0;i < response.questions.length;i++){
                                    self.paper_questions.push(response.questions[i]);
                                }
                                self.calc_questions_index();
                                if(typeof callback=='function'){
                                    callback(response)
                                }
                            }
                        })
                    },

                    calc_questions_index:function () {
                        for(var i =0, index=1; i < this.paper_questions.length; i++)
                        {
                            this.paper_questions[i].index = index;
                            index+=this.paper_questions[i].item_count;
                        }
                    },
                    questionMoveUp: function(index)
                    {
                        var item = this.paper_questions.splice(index, 1);
                        this.paper_questions.splice(index - 1, 0, item[0]);
                        this.calc_questions_index();
                    },
                    questionMoveDown: function(index)
                    {
                        var item = this.paper_questions.splice(index, 1);
                        this.paper_questions.splice(index + 1, 0, item[0]);
                        this.calc_questions_index();
                    },
                    questionMoveTo:function(index) {
                        var self = this;
                        Swal.fire({
                            title: '跳转到第几题的位置',
                            input: 'text',
                            inputAttributes: {autocapitalize: 'off'},
                            confirmButtonText: '确认',
                            showLoaderOnConfirm: true,
                            preConfirm: function(value)
                            {
                                var current_index= index + 1, _value= parseInt(value)
                                var question_total= self.paper_questions.length,
                                    error= false, error_message

                                if(_value < 1 || _value > question_total){
                                    error_message= '请输入正确的位置(1~' + question_total + ')';
                                    error= true
                                }
                                if(_value == current_index){
                                    error_message= '要移动的位置和当前位置一致，无须操作';
                                    error= true
                                }
                                if(error){
                                    return Swal.showValidationMessage(error_message)
                                }

                                return _value
                            }
                        }).then(function(result) {
                            var moveTo = result.value,
                                item = self.paper_questions.splice(index, 1)

                            self.paper_questions.splice(moveTo - 1, 0, item[0])
                            self.calc_questions_index();
                        })
                    },
                    questionDelete: function(index) {
                        this.paper_questions.splice(index,1);
                        this.calc_questions_index();
                    },
                    save: function(){
                        var self= this
                        var brief_question=[]
                        for(var i=0; i< this.paper_questions.length; i++){
                            brief_question[i]= {
                                question_id: this.paper_questions[i].id,
                                score: this.paper_questions[i].score
                            }
                        }

                        /**
                         * 错误逻辑判断均放在后台
                        */
                        var data= {
                            paper: this.form,
                            paper_questions: brief_question,
                        }


                        utils.swal.loading({
                            'title': '数据提交',
                            'text': '正在保存数据，请稍候',
                        })

                        if(paper.form_attributes.is_update==1){
                            data._method= 'put'
                        }

                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': paper._token
                            },
                            url: paper.form_attributes.action_url,
                            data: data,
                            type: 'POST',
                            success: function(response){
                                utils.swal.close()
                                if(response.status=='failed'){
                                    return utils.swal.error(response.message)
                                }
                                $.pjax({ url: paper.urls.paper_list, container: '#pjax-container' });
                            }
                        })
                    },
                }
            });
        };
        window.paper = paper;
    })(jQuery,window);

    $(function(){
        var data= {
            categories: <?php echo json_encode($data['categories'],JSON_UNESCAPED_UNICODE);?>,
            form: {
                category_id: "<?php echo $data['form']['category_id'];?>",
                customer_school_id: "<?php echo $data['form']['customer_school_id'];?>",
                alias: "<?php echo $data['form']['alias'];?>",
                title: "<?php echo $data['form']['title'];?>",
                is_public: <?php echo $data['form']['is_public'];?>,
                time_limit_enable: <?php echo $data['form']['time_limit_enable'];?>,
                time_limit_value: <?php echo $data['form']['time_limit_value'];?>,
                pass_score: <?php echo $data['form']['pass_score'];?>,
                total_score: <?php echo $data['form']['total_score'];?>,
            },
            paper_questions: [],
        }
        console.log(data)


        var action_url = "{{ $data['id']!=null ? route('exams.paper.update', ['paper'=>$data['id']]) : route('exams.paper.store') }}"
        paper.parameters(data)
            .formAttributes({
                action_url: action_url,
                is_update: "{{ $data['id'] ? 1 : 0 }}"
            })
            .set_urls({
                paper_list: "{{ route('exams.paper.index') }}",
                question_search: "{{route('exams.question.search')}}",
                question_preview: "{{route('exams.question.previews')}}",
            })
            .csrf_token("{{csrf_token()}}")
            .run()

        $(".paper-categories").select2({"allowClear":true, minimumResultsForSearch:-1 })
        $(".paper-gradation").select2({"allowClear":true, minimumResultsForSearch:-1 })

        $('.locked.la_checkbox').bootstrapSwitch({
            size:'small',
            onText: 'ON',
            offText: 'OFF',
            onColor: 'success',
            offColor: 'default',
            onSwitchChange: function(event, state) {
                var next_element= $(event.target).closest('.bootstrap-switch').next(),
                    name= next_element.attr('name')
                var value= state ?1 :0;
                next_element.val(value).change()
                paper.vueFormAttribute(name, value)
            }
        });
    })

</script>