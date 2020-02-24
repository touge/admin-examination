<style type="text/css">
    .question-filter{}
    .question-filter .basic{padding:10px;}
    .question-filter .basic .field{margin-right:10px;display:inline-block;}
</style>

<div id="question-select">
    <div class="box" style="border-radius:0;border-top:0">
        <div class="box-header with-border">
            <div class="question-filter">
                <div class="basic">
                    <div class="field">
                        <div class="form-group">
                            <select class="form-control" v-model="form.type" style="width: 150px;">
                                <option value="0">选择试题类型</option>
                                <option v-for="(type, index) in types" v-if="types" v-bind:value="index">@{{type}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <div class="form-group">
                            <input type="text" class="form-control" v-model="form.question" placeholder="输入试题检索" style="width:200px;" >
                        </div>
                    </div>
                    <div class="field">
                        <input type="checkbox" class="form-check" v-model="show_expert"> 高级搜索
                    </div>
                    <div class="field">
                        <a class="btn btn-sm btn-primary" v-on:click="request(1)">
                            <i class="fa fa-search"></i> 搜索
                        </a>
                    </div>
                </div>

                <div v-show="show_expert">
                    <table class="table">
                        <tbody>
                        @foreach($data['group_tags'] as $group)
                            <tr>
                                <td style="width:10%">{{$group['title']}}</td>
                                <td>
                                    @foreach($group['tags'] as $tag)
                                        <input type="checkbox" value="{{$tag['id']}}" v-model="form.tags"/>
                                        <span>{{$tag['title']}}</span>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.box-header -->

        <div class="box-body">
            <table class="table table-bordered">
                <tbody>
                <tr v-for="(item, index) in questions">
                    <td style="width: 10%" class="text-center">
                    <input type="checkbox" v-model="question_select_ids" v-bind:value="item.id">
                    </td>
                    <td> <span class="label label-info">@{{ types[item.type] }}</span> @{{ item.question }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="box-footer clearfix">
            <ul class="pagination pagination-sm no-margin">
                <li><a href="javascript:;" v-on:click="pagination('pre')" class="disabled"><i class="fa fa-angle-left"></i> 上一页</a></li>
                <li><span>@{{ paginate.current }}/ @{{ paginate.page_total }}</span> </li>
                <li><a href="#" v-on:click="pagination('next')">下一页 <i class="fa fa-angle-right"></i></a></li>
            </ul>
        </div>

        <!-- /.box-body -->
        <div class="overlay" v-if="loading">
            <i class="fa fa-refresh fa-spin"></i>
        </div>
    </div>

    <div class="modal-footer ">
        <div class="text-center">
            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            <button type="button" class="btn btn-primary" v-on:click="post_questions">确认</button>
        </div>
    </div>
</div>
<script>
    !(function($,window,undefined){
        var search= {
            vue: null,
            params: [],
        }
        search.parameters= function(params){
            this.params= params
            return this
        }
        search.run= function(element){
            this.vue = new Vue({
                el: element,
                data: this.params,
                methods:{
                    pagination: function(type)
                    {
                        var do_page;
                        if(type == 'pre'){
                            do_page= this.paginate.current - 1
                            if(do_page <= 0)
                            {
                                return helper.swal.error('已经是第一页了哟~')
                            }
                        }else{
                            do_page= this.paginate.current + 1
                            console.log(do_page)
                            if(do_page > this.paginate.page_total){
                                return helper.swal.error('已经是最后一页了哟~')
                            }
                        }
                        this.request(do_page)
                    },
                    post_questions: function(){
                        if(this.question_select_ids.length<=0)
                        {
                            return helper.swal.error('未选中任何试题!')
                        }
                        paper.receive_question_modal_data(this.question_select_ids)
                    },

                    request:function (page) {
                        var self= this
                        self.loading= true
                        page = page || 1;

                        var params= {
                            paginate: {
                                current: page,
                                limit: this.paginate.limit,
                            },
                            // page_size: this.page.size,
                            type: this.form.type,
                            question: this.form.question,
                            tags: this.form.tags,
                        };
                        $.ajax({
                            method: 'post',
                            headers: {
                                'X-CSRF-TOKEN': "{{csrf_token()}}"
                            },
                            data: params,
                            url: "{{route('exams.question.search')}}",
                            success: function(response){
                                self.loading= false
                                self.show_expert= false
                                // console.log(response)

                                self.questions= response.data
                                self.paginate.total= response.total
                                self.paginate.current= response.current_page
                                self.paginate.page_total= Math.ceil(self.paginate.total/self.paginate.limit)
                            }
                        })
                    }
                },
                mounted:function () {
                    this.request();
                }
            });
            return this
        }

        window.search = search;
    })(jQuery,window);

    $(function(){
        var data= {
            loading: false,
            show_expert:false,

            types: <?php echo json_encode($data['question_types'],JSON_UNESCAPED_UNICODE);?>,
            questions: [],
            question_select_ids:[],

            paginate: {
                total: 0,
                current: 0,
                page_total: 0,
                limit: <?php echo $data['paginate']['limit'];?>,
            },
            form: {
                type: 0,
                question: '',
                tags: [],
            },
        }
        search.parameters(data).run("#question-select")
    })
</script>