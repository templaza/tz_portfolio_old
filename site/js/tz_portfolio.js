//Sort
//function tzSortFilter(srcObj,desObj,order){
//    srcObj.sort(function(a,b){
//       var compA = jQuery(a).text().trim();
//       var compB = jQuery(b).text().trim();
//        if(jQuery(a).attr('data-option-value') != '*' &&
//                jQuery(b).attr('data-option-value') != '*' ){
//            if(order.substr(order.length-3,order.length).toLowerCase() == 'asc'){
//                return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
//            }
//            if(order.substr(order.length-4,order.length).toLowerCase() == 'desc'){
//                return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;
//            }
//        }
//    });
//    srcObj.each(function(idx, itm) { desObj.append(itm).append('\n');});
//};

function tzSortFilter(srcObj,desObj,order){
    if((!order || order == 'auto')
        && (srcObj.last().attr('data-order') && srcObj.last().data('order').toString().length)){
        order   = 'filter_asc';
    }
    srcObj.sort(function(a,b){
        var compA = jQuery(a).data('order')?parseInt(jQuery(a).data('order')):jQuery(a).text().trim();
        var compB = jQuery(b).data('order')?parseInt(jQuery(b).data('order')):jQuery(b).text().trim();
        if(jQuery(a).attr('data-option-value') != '*' &&
            jQuery(b).attr('data-option-value') != '*' ){
            if(order.substr(order.length-3,order.length).toLowerCase() == 'asc'){
                return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
            }
            if(order.substr(order.length-4,order.length).toLowerCase() == 'desc'){
                return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;
            }
        }
    });
    srcObj.each(function(idx, itm) { desObj.append(itm).append('\n');});
};

function ajaxComments($element,itemid,text,link){
    if($element.length){
        if($element.find('.name a').length){
            var url = 'index.php?option=com_tz_portfolio&task=portfolio.ajaxcomments',
                $href   = Array(),
                $articleId  = Array();
            if(link){
                url = link;
            }
            $element.map(function(index,obj){
                if(jQuery(obj).find('.name a').length){
                    if(jQuery(obj).find('.name a').attr('href').length){
                        $href.push(jQuery(obj).find('.name a').attr('href'));
                        if(jQuery(obj).attr('id')){
                            $articleId.push(jQuery(obj).attr('id'));
                        }
                    }
                }
            });

            jQuery.ajax({
                type: 'post',
                url: url,
                data: {
                    Itemid: itemid,
                    url: Base64.encode(JSON.encode($href)),
                    id: Base64.encode(JSON.encode($articleId))
                }
            }).success(function(data){
                if(data && data.length){
                    var $comment    = JSON.decode(data);
                    if(typeof $comment == 'object'){
                        jQuery.each($comment,function(key,value){
                            if(jQuery('#'+key).find('.TzPortfolioCommentCount').length){
                                jQuery('#'+key).find('.TzPortfolioCommentCount').html(text+'<span>'+value+'</span>');
                            }
                        });
                    }
                }
            });
        }
    }
};