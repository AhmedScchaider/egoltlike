/**
 * @package   	Egolt Like
 * @link 		http://www.egolt.com
 * @copyright 	Copyright (C) Egolt www.egolt.com
 * @author    	Soheil Novinfard
 * @license    	GNU/GPL 2
 *
 * Name:		Egolt Like
 * License:    	GNU/GPL 2
 * Project: 	http://www.egolt.com/products/egoltlike
 */
 
function EgoltLike(id, lval, liken, dliken, shown){

	var currentURL = window.location;

	var live_site = currentURL.protocol+'//'+currentURL.host+rooturi;

	var lsXmlHttp = '';
	
	var loaddiv = document.getElementById('eloading_'+id);
	// var sumdiv = document.getElementById('egoltlike_'+id).getElementById('sum_grid').getElementById('esum');
	var sumdiv = document.getElementById('esum_'+id);
	var lastsum = sumdiv.innerHTML;
	if(shown)
	{
		// var posdiv = document.getElementById('egoltlike_'+id).getElementById('pos_grid').getElementById('elike_val');
		var posdiv = document.getElementById('elike_val_'+id);
		// var negdiv = document.getElementById('egoltlike_'+id).getElementById('neg_grid').getElementById('edislike_val');	
		var negdiv = document.getElementById('edislike_val_'+id);	
	}
	
	sumdiv.innerHTML='<img src="'+live_site+'/media/egoltlike/images/loading4.gif" border="0" align="absmiddle"/>';
			
		try	{
			lsXmlHttp=new XMLHttpRequest();
		} catch (e) {
			try	{ lsXmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try { lsXmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {
					alert('text0');
					return false;
				}
			}
		}

	if ( lsXmlHttp != '' ) {
		lsXmlHttp.onreadystatechange=function() {
			var response;
			if(lsXmlHttp.readyState==4){
				setTimeout(function(){ 
					response = lsXmlHttp.responseText; 
					if(response=='thanks'){
						loaddiv.innerHTML='<small>'+eg_th_str+'</small>';
						if(lval=='1'){
							if(shown)			
								posdiv.innerHTML=liken+1;
							var sum = liken-dliken+1;								
						}
						if(lval=='2'){
							if(shown)
								negdiv.innerHTML=dliken+1;
							var sum = liken-dliken-1;								
						}
						if(sum>0)
						{
							sum='+'+sum;
							sumdiv.className='esum pos';
						}
						else if(sum<0)
						{
							sumdiv.className='esum neg';
						}
						else
						{
							sumdiv.className='esum neu';						
						}
						sumdiv.innerHTML=sum;						
					}
					if(response=='liked'){
						loaddiv.innerHTML='<small>'+eg_vt_str+'</small>';
						sumdiv.innerHTML=lastsum;
					}
					if(response=='noaccess'){
						loaddiv.innerHTML='<small>'+eg_ac_str+'</small>';
						sumdiv.innerHTML=lastsum;
					}
				},500);
			}
		}
		lsXmlHttp.open("GET",live_site+"/plugins/content/egoltlike/egoltlike_ajax.php?task=like&lval="+lval+"&cid="+id,true);
		lsXmlHttp.send(null);
	}
	
}
