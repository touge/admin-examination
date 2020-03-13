
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
window.utils = utils;
})(jQuery,window);