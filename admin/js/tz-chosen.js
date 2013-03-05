(function($) {
    return $.fn.tzChosen = function(options) {
        var selected = $(this),
                inputName   = $(this).attr('name'),
                _name   = inputName.slice(0).replace(/\[\]/,''),
                $var = $.extend({}, $.fn.tzChosen.defaults, options);
        $.fn.tzChosen.defaults  = {
            source : [],
            sourceEdit: [],
            items: 8,
            minLength: 1,
            updater: function(item){},
            highlighter: function(items){},
            matcher: function(item){}
        }

        $var.updater    = function(item){
            addElement(newsource.indexOf(item),item);
            $(this).width(25);
            cursource[cursource.indexOf(item.toLowerCase())]='';

            if(options.updater){
                eval(options.updater.toString().replace(/function+\s?\(\)\{/i,'').replace(/\}$/,''));
            }

            return null;
        }

        var cursource= $var.source.slice(0),
                newsource = $var.source.slice(0),
                medsource   = $var.source.slice(0);

        $(this).removeAttr('name');

        var _html    = '<div id="tz_chosen_'+_name+'" class="chzn-container chzn-container-multi">';
            _html += '<ul class="chzn-choices"><li class="search-field">';
            _html += '</li></ul></div>';

        $(_html).insertBefore($(this));

        var main    = $('#tz_chosen_'+_name);
        main.find('.search-field').append($(this));
        main.find('.chzn-choices').width($(this).width());
        
        var addElement  = function(index,value){
            var html    = '<li id="tz_chosen_t_'+index+'" class="search-choice">';
            html += '<span>'+value+'</span>';
            html += '<a class="search-choice-close" rel="'+index+'" href="javascript:void(0)"></a>';
            html += '<input type="hidden" name="'+inputName+'" value="'+value.toLowerCase().trim()+'"/>';
            html += '</li>';
            $(html).insertBefore(main.find('.chzn-choices').find('.search-field'));
            selected.removeAttr('value');
            selected.focus();
        }

        main.find('.chzn-choices').live('click',function(){
            selected.focus();
        })

        main.find('.search-choice-close').live('click',function(){
            var index = $(this).attr('rel'),
                    nitem = $('#tz_chosen_t_'+index);
            if(main.find('.search-choice').length ==  1){
                selected.width('auto');
            }
            if(medsource.indexOf(nitem.find('input').attr('value').toLowerCase()) != -1){
                cursource[index]  = medsource[index];
            }
            else{
                newsource.splice(index,1);
            }
            nitem.remove();
        });

        $(this).typeahead({
            source: function(query,process){
                process(cursource);
            },
            items: $var.items,
            minLength: $var.minLength,
            updater: $var.updater,
            highlighter:$var.highlighter,
            matcher:$var.matcher
        })

        if($var.sourceEdit){
            $.each($var.sourceEdit,function(index,value){
                addElement(cursource.indexOf(value.toLowerCase()),value);
                cursource[cursource.indexOf(value.toLowerCase())]='';
            })
            $(this).width(25);
        }

        return $(this).bind("keyup",function(e){
            if(e.which != 13){
                $(this).width(25 + $(this).attr('value').length * 4);
            }
            if(e.which == 13){
                var cval    = $(this).attr('value');
                if(cval.length){
                    if(newsource.indexOf(cval.toLowerCase()) == -1){
                        newsource.push(cval.toLowerCase());
                        addElement(newsource.length-1,cval);
                        $(this).width(25);
                    }
                }
            }
        })
    }
})(jQuery);


//jQuery(document).ready(function(){
//    jQuery('#tz_tags .chzn-choices').width(jQuery('#tz_tags').find('.search-field input').width());
//    var addElement  = function(value,index){
//        var html    = '<li id="tz_tags_t_'+index+'" class="search-choice">';
//        html += '<span>'+value+'</span>';
//        html += '<a class="search-choice-close" rel="'+index+'" href="javascript:void(0)"></a>';
//        html += '<input type="hidden" name="tz_tags[]" value="'+value.toLowerCase().trim()+'"/>';
//        html += '</li>';
//        jQuery(html).insertBefore(jQuery('#tz_tags').find('.search-field'));
//        jQuery('#tz_tags').find('.search-field input').attr('value','');
//        jQuery('#tz_tags').find('.search-field input').focus();
//    }
//
//    var subject = [],
//            medsubject = subject.slice(0),
//            msubject = subject.slice(0);
//    jQuery('.search-choice-close').live('click',function(){
//        var index   = jQuery(this).attr('rel');
//        if(medsubject.indexOf(jQuery('#tz_tags_t_'+index).find('input').attr('value').toLowerCase()) != -1){
//            subject[index]  = medsubject[index];
//        }
//        else{
//            msubject.splice(index,1);
//        }
//        jQuery('#tz_tags_t_'+index).remove();
//    })
//
//    jQuery('.suggest').typeahead({
//        source: function(query,process){
//            bool    = false;
//            process(subject);
//        },
//        updater: function(item){
//            addElement(item,msubject.indexOf(item));
//            jQuery('#tz_tags').find('.search-field input').width(25);
//            subject[subject.indexOf(item.toLowerCase())]='';
//            return null;
//        }
//    })
//    jQuery('#tz_tags').find('.search-field input').bind("keyup",function(event){
//        jQuery('#tz_tags').find('.search-field input').width(25);
//        if(event.which != 13){
//            jQuery('#tz_tags').find('.search-field input').width(jQuery('#tz_tags').find('.search-field input').width()+4);
//        }
//
//        if(event.which == 13){
//            var cval    = jQuery(this).attr('value');
//            if(cval.length){
//                if(msubject.indexOf(cval.toLowerCase()) == -1){
//                    msubject.push(cval.toLowerCase());
//                    addElement(cval,msubject.length-1);
//                }
//            }
//        }
//    })
//})