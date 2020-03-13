!(function($, Vue, window, undefined){
    var directive= {
        select2: function(){
            Vue.directive('select2', {
                inserted: function (el, binding, vnode) {
                    var options = binding.value || {};
                    $(el).select2(options).on("select2:select", function(e){
                        el.dispatchEvent(new Event('change', {target: e.target}));

                    })
                    ;
                },
                update: function (el, binding, vnode) {
                    for (var i = 0; i < vnode.data.directives.length; i++) {
                        if (vnode.data.directives[i].name == "model") {
                            $(el).val(vnode.data.directives[i].value);

                        }
                    }
                    $(el).trigger("change");

                }
            })
        }
    }

    /**
     * 执行
     */
    directive.select2();

})(jQuery, Vue, window);