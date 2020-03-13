
/**
 * Created by yuanlong on 2017/3/27.
 */
!(function($,window,undefined){
    'use strict';
    var utils = {};

    utils.swal= {
        error: function(message){
            Swal.fire({
                html: "<strong>" + message + "</strong>",
                type: 'error',
                showConfirmButton: false,
                timer: 2000
            })
        },
        loading: function(params){
            Swal.fire({
                title: params.title,//'数据提交',
                text: params.text,//'正在保存数据，请稍候',
                allowOutsideClick: false,
                onBeforeOpen: function(){
                    Swal.showLoading()
                }
            })
        },
        close: function(){
            Swal.close()
        }
    }

    utils.uuid = function(len, radix) {
        var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split('');
        var uuid = [], i;
        radix = radix || chars.length;

        if (len) {
            // Compact form
            for (i = 0; i < len; i++) uuid[i] = chars[0 | Math.random()*radix];
        } else {
            // rfc4122, version 4 form
            var r;

            // rfc4122 requires these characters
            uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
            uuid[14] = '4';

            // Fill in random data. At i==19 set the high bits of clock sequence as
            // per rfc4122, sec. 4.1.5
            for (i = 0; i < 36; i++) {
                if (!uuid[i]) {
                    r = 0 | Math.random()*16;
                    uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r];
                }
            }
        }

        return uuid.join('');
    }

    /**
     * 封装boostrap.modal，以具体远程打开能力及快捷操作
     */
    utils.modal = function(params)
    {
        var default_options = {
            url : null,
            keyboard: false,
            data: {},
            dataType: 'html',
            modal_id : null,
            title: 'Modal Window',
            size: '',
            method: 'get',
            shown: undefined,
            hidden: undefined,
            show: undefined
        };
        var options = $.extend(default_options, params);
        if( options.modal_id == null )
        {
            options.modal_id = "__boost_win_id_" + $(".modal").length;
        }

        if($(".modal-backdrop").length>0){
            $(".modal-backdrop").remove()
        }

        $.ajax({
            url: options.url,
            dataType: options.dataType,
            data: options.data,
            method: options.method,
            success: function(response){
                var modal_tmpl= '    <div class="modal-dialog '+options.size+'">\n' +
                    '        <div class="modal-content">\n' +
                    '            <div class="modal-header">\n' +
                    '                <button type="button" class="close" data-dismiss="modal" aria-label="Close">\n' +
                    '                    <span aria-hidden="true">&times;</span></button>\n' +
                    '                <h4 class="modal-title">'+options.title+'</h4>\n' +
                    '            </div>\n' + response +
                    '            </div>\n' +
                    '        </div>\n' +
                    '    </div>';
                $(document.body).append('<div class="modal fade" id="'+options.modal_id+'"></div>')
                $("#"+options.modal_id).append(modal_tmpl).modal({
                    keyboard: false,
                    show: false,
                    // backdrop: 'static',
                }).on("shown.bs.modal" ,function(e){
                    e.preventDefault()
                    typeof options.shown=='function' && options.shown(options.modal_id)
                }).on("hidden.bs.modal" ,function(){
                    typeof options.hidden== 'function' && options.hidden(options.modal_id)
                    $(this).remove()
                })

                if(typeof options.show=='function'){
                    options.show(options.modal_id)
                    setTimeout(function(){
                        $("#"+options.modal_id).modal('show')
                    },100)
                }else{
                    $("#"+options.modal_id).modal('show')
                }
            }
        });
    };

    window.utils = utils;
})(jQuery,window);