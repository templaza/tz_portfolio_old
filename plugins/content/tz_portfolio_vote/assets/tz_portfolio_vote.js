/*------------------------------------------------------------------------
# plg_extravote - ExtraVote Plugin
-------------------------------------------------------------------------*/
function JVXVote(id,i,total,total_count,xid,counter){
	var currentURL = window.location;
	var live_site = currentURL.protocol+'//'+currentURL.host+sfolder;
	var lsXmlHttp = '';
	
	var div = document.getElementById('TzVote_'+id+'_'+xid);
	if (div.className != 'TzVote-count voted') {
		div.innerHTML='<img src="'+live_site+'/plugins/content/tz_portfolio_vote/images/loading.gif" border="0" align="absmiddle" /> '+'<small>'+TzVote_text[1]+'</small>';
		try	{
			lsXmlHttp=new XMLHttpRequest();
		} catch (e) {
			try	{ lsXmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try { lsXmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {
					alert(TzVote_text[0]);
					return false;
				}
			}
		}
	}
	div.className = 'TzVote-count voted';
	if ( lsXmlHttp != '' ) {
		lsXmlHttp.onreadystatechange=function() {
			var response;
			if(lsXmlHttp.readyState==4){
				setTimeout(function(){ 
					response = lsXmlHttp.responseText; 
					if(response=='thanks') div.innerHTML='<small>'+TzVote_text[2]+'</small>';
					if(response=='login') div.innerHTML='<small>'+TzVote_text[3]+'</small>';
					if(response=='voted') div.innerHTML='<small>'+TzVote_text[4]+'</small>';
				},500);
				setTimeout(function(){
					if(response=='thanks'){
						var newtotal = total_count+1;
						var percentage = ((total + i)/(newtotal));
						document.getElementById('rating_'+id+'_'+xid).style.width=parseInt(percentage*20)+'%';
					}
					if(counter!=0){
						if(response=='thanks'){
							if(newtotal!=1)	
								var newvotes=TzVote_text[5].replace('%s', newtotal );
							else
								var newvotes=TzVote_text[6].replace('%s', newtotal );
							div.innerHTML='<small>( '+newvotes+' )</small>';
						} else {
							if(total_count!=0 || counter!=-1) {
								if(total_count!=1)
									var votes=TzVote_text[5].replace('%s', total_count );
								else
									var votes=TzVote_text[6].replace('%s', total_count );
								div.innerHTML='<small>( '+votes+' )</small>';
							} else {
								div.innerHTML='';
							}
						}
					} else {
						div.innerHTML='';
					}
				},2000);
			}
		}
		lsXmlHttp.open("GET",live_site+"/plugins/content/tz_portfolio_vote/assets/ajax.php?task=vote&user_rating="+i+"&cid="+id+"&xid="+xid,true);
		lsXmlHttp.send(null);
	}
}