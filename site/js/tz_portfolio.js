//Sort
function tzSortFilter(srcObj,desObj,order){
    srcObj.sort(function(a,b){
       var compA = jQuery(a).text().trim();
       var compB = jQuery(b).text().trim();
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