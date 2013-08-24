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
            keys: null,
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

        var cursource= $var.source?$var.source.slice(0):[],
                newsource = $var.source?$var.source.slice(0):[],
                medsource   = $var.source?$var.source.slice(0):[];

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
        });

        if($var.sourceEdit){
            $.each($var.sourceEdit,function(index,value){
                addElement(cursource.indexOf(value.toLowerCase()),value);
                cursource[cursource.indexOf(value.toLowerCase())]='';
            })
            $(this).width(25);
        }

        // Get key
        var strKeysEqual ='e.which == 13', strKeysNotEqual = 'e.which != 13';
        if($var.keys && $var.keys.length){
            $var.keys.each(function(value,index){
                strKeysEqual    += ' || e.which - 144 =='+value.charCodeAt(0);
                strKeysNotEqual += ' && e.which - 144 !='+value.charCodeAt(0);
//                if(index != $var.keys.length - 1){
//                    strKeysEqual    += '';
//                }
            })
        }

        return $(this).keyup(function(e){
            if(!$('#tz_chosen_'+inputName.replace('[]','')+' > .typeahead').length){
                $('#tz_chosen_'+inputName.replace('[]','')).append($('#tz_chosen_'+inputName.replace('[]',''))
                    .find('.chzn-choices .typeahead'));
            }
            if(eval(strKeysNotEqual)){
                $(this).width(25 + $(this).attr('value').length * 4);
            }
            if(eval(strKeysEqual)){
                var cval    = $(this).attr('value');
                if(cval.length){
                    if(newsource.indexOf(cval.toLowerCase()) == -1){
                        newsource.push(cval.toLowerCase());
                        addElement(newsource.length-1,cval);
                        $(this).width(25);
                        $('.typeahead').css({'display': 'none'});
                    }else{
                        $('.typeahead').css({'display': 'none'});
                    }

                }
            }
        }).keydown(function(e){
            if(eval(strKeysEqual) && e.which != 13){
                if($('.typeahead').css('display') != 'none'){
                    var cval    = $(this).attr('value');
                    if($('.typeahead').find('li').hasClass('active')){
                        cval  = $('.typeahead').find('li.active').attr('data-value');
                        if(newsource.indexOf(cval.toLowerCase()) != -1){
                            addElement(cursource.indexOf(cval.toLowerCase()),cval);
                            $(this).width(25);
                            cursource[cursource.indexOf(cval.toLowerCase())]='';
                        }
                    }
                }
                e.preventDefault();
            };
        });
    }
})(jQuery);