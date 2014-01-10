(function($){

$.fn.datatable = function(options){
		
	this.each(function(){
		
		var thiselement = $(this);
		var thisid = $(this).attr('id');
		var usettings = options;
		
		
		var defaults = {
		  cwidth : '980',
		  pheader : true,
		  searchquery : {searchkey:"",limit:10,page:1,order:"item_name",direc:"ASC",allshop:"0",totalrows:null},
		  urls : {},
		  buttons : {},
		  popitems : {},
		  pagetype :''		  
   		};
		
		var settings = $.extend(true, {}, defaults, usettings); 
		// var rno = Math.floor(Math.random() * (1 - 20 + 1)) + 1;
		window[thisid] = settings;
		
		//window[thisid].searchquery.limit 

	var get1more = {searchkey:"",limit:"" ,page:"",order:"",direc:"",allshop:""};

	if(window[thisid].pheader == true){
		pgheader = function(){
			var h = '';
			h += '<div class="pheader">'
			+ '<span class="input-group" style="float:left; width:500px;">'
			+ '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
			+ '<input type="text" id="search" class="form-control" /> '
			+ '<span class="input-group-addon" style="border-left:0px; border-right:0px;">Limit</span>'
			+ ''
			+ '<select id="limit"  class="form-control"><option>10</option><option>20</option><option>30</option><option>40</option></select>'
			// if(window[thisid].pagetype == 'products'){
			// 	h += '			<span class="input-group-addon">All Shops</span>';
			// 	h += '			<select class="form-control" id="allshops" ><option value="0">No</option><option value="1">Yes</option></select>';
			// } 
			+ '</span>'
			+ '<button class="btn btn-default addnew" style="margin-left:5px; float:left;"> Add New </button>'
			+ '<div style="position:relative; float:left; width:38px; "><div class="sspin" id="gload" ></div></div>'
			+ '<div style="clear:both"></div></div>';
			return h;
		}
	}else {  pgheader = function(){ return ''; }; }
	
	theader = function(feilds){
		var h ='';
		h += '<div class="mytable box-outer-shadow" style="width:'+ window[thisid].cwidth +';"><div id="tblheader">';
		for (key in feilds) {
			var n = feilds[key].split(",");
			var s = n[1].match(/_s_[\w]+/g);
			var w = feilds[key].match(/_w_[\w]+/g);
			if(s != '_s_nosort'){ var s = 'sort';}else { var s = ''; }
			if(w){ w = w[0].substr(3); w = 'width:'+ w +'px;';}else{w = '';}
			h += '<div class="rowcell header '+ s +'" style="'+ w +'" id="'+key+'">'+ n[0] +'<div></div></div>' ;
		}
		h += '<div style="clear:both"></div></div>';
		return h;
	}
	
	tbody = function(data,thisid){
		var str = '';
		if(data['totalrows'] != 0){
		for (var i = 0; i < data['itemdata'].length; i++) {
			var phead = ''; var pend = '';
			if(jQuery.isEmptyObject(window[thisid].popitems) === false){
				var ptxt = '';
				for (pitem in window[thisid].popitems){
					if(data['itemdata'][i][pitem] != '') { ptxt += '<strong>'+ window[thisid].popitems[pitem] +' :</strong> '+data['itemdata'][i][pitem] + '<br />'; }
				}
				phead = '<a class="hpopover" data-content="'+ ptxt +'">';
				pend = '</a>';
			}
			str += '<div id="item_'+  data['itemdata'][i][Object.keys(data['itemdata'][i])[0]] +'" style="width:'+ window[thisid].cwidth +';" class="datarow">';
			var no = 1;
			for (key in window[thisid].feilds) {
				var a = window[thisid].feilds[key].match(/_a_[\w]+/g);
				var dd = window[thisid].feilds[key].match(/_dd_[\w]+/g);
				var hwidth = $('#'+thisid+' #'+key).width()+31;
				if(a){ a = a[0].substr(3); a = 'text-align:'+ a;   }else{a = '';}
				if(dd){ dd = dd[0].substr(4);  var dkey = dd;  }else{ var dkey = key; }
				if(key != 'addss') { 
					str += '<div class="rowcell '+ key +'" style="width:'+ hwidth +'px;'+ a +'">';
					if(no == 2) str += phead;
					str += data['itemdata'][i][dkey];
					if(no == 2) str += pend;
					str += '</div>';
				}
				if(key == 'addss') { 
					str += '<div class="rowcell addss" style="width:'+ hwidth +'px; text-align:center; padding:3px;">'+ mkbutton(window[thisid].buttons,data['itemdata'][i][Object.keys(data['itemdata'][i])[0]]) +'</div>';
				}
				no++;
			}
			str += '<div style="clear:both;"></div></div>';
			
		};
		}else { str += '<div>No Matching Data</div>'; $('.load').fadeOut();}
		
		return str;
	}
	
	ajaxcall = function(t,delid,thisb){
		if (typeof thisb !== 'undefined') { thisid = thisb; }
		$('#'+thisid+' .sspin').fadeIn();
		if(t == 2){ querystring = get1more; } else { querystring = window[thisid].searchquery; }
		//console.log(querystring);
		$.get(window[thisid].urls.geturl,querystring,
			function(data){
				if (typeof thisb !== 'undefined') {window[thisid] = window[thisb]; }
				var data= $.parseJSON(data);
				window[thisid].searchquery.totalrows = data['totalrows'];
				
				if(typeof thisb === 'undefined'){ 
					divn =  $('#'+thisid).find('.norow');
				}else{ 
					divn =  $('#'+thisb).find('.norow');
					thisid = thisb;
				}
				
				$(divn).html('<button class="btn btn-default disabled">#rows</button><button id="total" class="btn btn-default disabled">' + data['totalrows'] + '</button>');
				if(t == 2){
					if($('.datarow').length < window[thisid].searchquery.limit || data['itemdata'] == 'NoData') {	}else{
					$(divb).append(tbody(data,thisid));
						var mainid = '#item_'+data['itemdata'][0][0];
						$(mainid).hide();
						$(mainid).slideDown();
					}
					$(delid).slideUp(function(){
						$(this).remove();
						pagination(window[thisid].searchquery.totalrows,thisid);
						if($('.datarow').length == 0) { window[thisid].searchquery.page = parseFloat(window[thisid].searchquery.page) - 1;  
							ajaxcall();
						}
					});
				}else{
					divb =  '#'+thisid+' .tblbody';
					$(divb).html(tbody(data,thisid));
					if(t!=0) pagination(window[thisid].searchquery.totalrows,thisid);
				}
					makeheightlight(thisid);
					//jqbtn();
					$('.hpopover').popover({ trigger: "hover focus",html:true });
					$('#'+thisid+' .sspin').fadeOut();
			}
		);

	}

	
	pagination = function(totalrows,thisid){
		var pagecount = Math.ceil(totalrows/window[thisid].searchquery.limit);
		var divp = $('#'+thisid).find('.pignate');
		$(divp).pagination({
		  total_pages: pagecount,
		  display_max: 4,
		  current_page: parseInt(window[thisid].searchquery.page),
		  callback: function(event, page) {
			window[thisid].searchquery.page = page;
			ajaxcall(0,'',thisid);
		  }
		});
	}
	
	makeheightlight = function(thisid){
		var hid = '#'+window[thisid].searchquery.order;
		var hclass = '.'+window[thisid].searchquery.order;
		$('#'+thisid+' .sort').css('backgroundColor','#fff');
		$('#'+thisid+' '+hclass+','+hid).css({backgroundColor: "rgba(0,0,0,0.02)"});
		$('#'+thisid+' .sort').children('div').css({backgroundPosition:'-128px 0px'});
		posi = (window[thisid].searchquery.direc == 'ASC') ? '-96px -192px' : '-64px -192px';
		$(hid).children('div').css({backgroundPosition:posi});
	};

	mkbutton = function(buttons,itemid){
		var btnn = '';
		btnn += '<span class="btn-group dtbtn">';
		for (key in buttons) {
			if(key == 'del') btnn += '<span id="itemd_'+  itemid +'" class="'+ key +' btn "><i class="glyphicon glyphicon-remove"></i></span>'; 
			if(key == 'edit') btnn += '<a rel="tab" href="'+ window[thisid].urls.editurl + itemid +'" id="itemd_'+  itemid +'" class="'+ key +' btn "><i class="glyphicon glyphicon-pencil"></i></a>'; 
			if(key == 'bar') btnn += '<span id="itemd_'+  itemid +'" class="'+ key +' abtn btn btn-default"><i class="glyphicon glyphicon-barcode"></i></span>'; 
			if(key == 'addd') btnn += '<span id="itemd_'+  itemid +'" class="'+ key +' abtn btn btn-default"><i class="glyphicon glyphicon-plus"></i></span>'; 
			if(key == 'hist') btnn += '<span id="itemd_'+  itemid +'" class="'+ key +' abtn btn btn-default"><i class="glyphicon glyphicon-book"></i></span>'; 
			//}else{ btnn += '<button id="itemd_'+  itemid +'" class="'+ key +' minibt"></button>' }
//			if(key != 'del') { btnn += '<button id="itemd_'+  itemid +'" class="'+ key +' minibt abtn"></button>'; 
//			}else{ btnn += '<button id="itemd_'+  itemid +'" class="'+ key +' minibt"></button>' }
		}
		btnn += '</span>';
		return btnn;
	}
	
	jqbtn = function(){
//		$('.hist').button({icons: {primary: "ui-icon-note" },text: false});
//		$('.addd').button({icons: {primary: "ui-icon-plusthick" },text: false});
//		$('.edit').button({icons: {primary: "ui-icon-pencil" },text: false});
//		$('.del').button({icons: {primary: "ui-icon-trash" },text: false});
//		$('.bar').button({icons: {primary: "" },text: false});
	};

	function loadmodal(url){
	$('#modalholder').remove();
	$('body').append('<div id="modalholder" class="modal fade" ></div>');
		$.get(url, function(data) {
			$('#modalholder').html(data);
			$('#modalholder').modal({
				//backdrop:false
			}).css({
				//width: 'auto',
				// 'margin-left': function () {
				//  return -($(this).width() / 2);
				// }
			});
			//console.log($('.modal').width());
		});
	}
	// $(thiselement).on('mouseover','.datarow',function(){
	// 	$(this).toggleClass( "active" );
	// });
	$(thiselement).on('change','#limit',function(){
		var thisid = $(this).parents('.dtable').attr('id');
		window[thisid].searchquery.page = 1;
		window[thisid].searchquery.limit = $(this).val();
		ajaxcall(1,'',thisid);	
	});
	
	$(thiselement).on('change','#allshops',function(){
		var thisid = $(this).parents('.dtable').attr('id');
		window[thisid].searchquery.page = 1;
		window[thisid].searchquery.allshop = $(this).val();
		ajaxcall(1,'',thisid);	
	});
	
	$(thiselement).on('keydown','#search',function(){
		var thisid = $(this).parents('.dtable').attr('id');
		window[thisid].searchquery.page = 1;
		window[thisid].searchquery.searchkey = $(this).val();
		ajaxcall(1,'',thisid);
	});

	$(thiselement).on('click','.sort',function(){
		var thisid = $(this).parents('.dtable').attr('id');
		if(window[thisid].searchquery.order == $(this).attr("id"))
		{
			direction = (window[thisid].searchquery.direc == 'ASC') ? 'DESC' : 'ASC';
			window[thisid].searchquery.direc = direction;
		}else{
			window[thisid].searchquery.order = $(this).attr("id");
			window[thisid].searchquery.direc = 'DESC'
			}
		ajaxcall(0,'',thisid);
	});
	
	$(thiselement).on('click',".del",function(){
		var thisid = $(this).parents('.dtable').attr('id');
		var pagel = parseFloat(window[thisid].searchquery.limit) * parseFloat(window[thisid].searchquery.page);
		get1more.searchkey=window[thisid].searchquery.searchkey; get1more.limit=1; get1more.page=pagel; get1more.order=window[thisid].searchquery.order; get1more.direc=window[thisid].searchquery.direc; get1more.allshop=window[thisid].searchquery.allshop;
		var thisid1 = $(this).attr("id");
		var id = thisid1.substr(6);
		var mainid = '#item_'+id;
		bootbox.confirm("Are you sure?", function(result) {
			if(result == true) {
				$.post(window[thisid].urls.delurl,{itemID:id},
				function(data){
					 ajaxcall(2,mainid);  								
				});
			}
		})
		.css({
				width: 'auto',
				'margin-left': function () {
				 return -($(this).width() / 2);
				}
		});
					
	});
	
	
	$(thiselement).on('click',".abtn",function(){
		var tid = thiselement.find(this).attr("id");
		var id = tid.substr(6);
		var mainid = '#item_'+id;
		var tclass = thiselement.find(this).attr('class');
		var st = tclass.split(' ');
		var btype = st[0];
		// console.log(window[thisid]);
		var url = '';
		if(btype == 'hist') url = window[thisid].urls.histurl + id;
		if(btype == 'edit') url = window[thisid].urls.editurl + id;
		if(btype == 'del') url = window[thisid].urls.delurl + id;
		if(btype == 'addd') url = window[thisid].urls.restock + id;
		if(btype == 'bar') url = window[thisid].urls.barurl + id;
		loadmodal(url);
		
	});
	
	$(thiselement).on('click','.addnew',function(){
		var thisid = $(this).parents('.dtable').attr('id');
		loadmodal(window[thisid].urls.addnew);
		console.log('adfas');
	});


	// limit = ($('#response').height() - 120) / 35;
	//if((Math.floor(limit) <= window[thisid].searchquery.totalrows) && Math.floor(limit) > 4 && Math.floor(limit) != window[thisid].searchquery.limit){
	//	window[thisid].searchquery.limit = Math.floor(limit);
	//}

	// $(window).resize(function(){
	// 	var limit = ($('#response').height() - 120) / 35;
	// 	if((Math.floor(limit) <= window[thisid].searchquery.totalrows) && Math.floor(limit) > 7 && Math.floor(limit) != window[thisid].searchquery.limit){
	// 		window[thisid].searchquery.limit = Math.floor(limit);
	// 		ajaxcall(1,'',thisid);
	// 	}		
	// });


	ajaxcall(1,'',thisid);
		var structure  = '<span class="tblbody"></span></div><div id="tblfooter" style="width:'+ window[thisid].cwidth +';"><div class="pignate"></div><span class="btn-group norow norow" style="float:right;"></span><div style="clear:both;"></div>';	


	$(thiselement).html(pgheader()+theader(window[thisid].feilds)+structure);
});	
}	

})(jQuery);