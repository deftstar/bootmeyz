<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Bootmeyz</title>
	<style type="text/css" title="currentStyle">
		@import "css/bootstrap.css";
		@import "css/font-awesome.min.css";
		@import "css/bootmeyz.css";
	</style>
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>

	 <script src="js/jquery-bootstrap-pagination.js" type="text/javascript"></script>
	 	<script type="text/javascript" src="js/bootbox.js"></script>
	<script type="text/javascript" src="js/my.datatable.js"></script>
</head>
<body>
<script type="text/javascript">
$(function(){
$('#datatables').datatable({
	cwidth:922,
	//popitems : {remarks:"Remarks",discount:"Discount",warrenty:"Warrenty",taxcat:"Tax Cat"}	,
	buttons : {edit:"",del:""},
	feilds : {fullname:"Name,_w_330, _a_left",shortname:"Short name,_w_330, _a_left",starrating:"Star, _w_70,",totalrooms:"Total Rooms,","addss":" ,_s_nosort, _w_70"},
	urls : {geturl:"../json.misc.php?action=getprop",delurl:"misc.php?action=deletehotels",editurl:"hotels.php?action=edit&itemID=",restock:"update.php?action=restock&itemID=",addnew:"addhotels.php",histurl:"inhistory.php?itemID=",barurl:"../../barcode/?itemid="},		
	searchquery : {searchkey:"",limit:"10",page:"1",order:"fullname",direc:"ASC",totalrows:null}
});
});
</script>
<div id="response" class="container">
<br/>
<div class="bootmeyz">
	<!-- <div class="btn-toolbar" role="toolbar">
		<div class="btn-group pull-left">
			<div class="meyz-limit form-group">
				<div class="input-group">
					<span class="input-group-addon">Limit</span>
					<select name="" class="form-control input-sm"><option value="">Auto</option><option value="">20</option><option value="">30</option><option value="">All</option></select>
				</div>
			</div>
		</div>
		<div class="btn-group pull-right">
			<button class="btn btn-default btn-sm"><i class="fa fa-plus"></i> Add</button>
		</div>
		<div class="btn-group pull-right">
			<div class="meyz-search form-group">
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-search"></i></span>
					<input type="text" name="" id="search" class="form-control input-sm" />
				</div>
			</div>
		</div>
	</div> -->
<nav class="navbar navbar-default meyz-nav" role="navigation">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".meyz-nav-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>

		<span class="navbar-brand meyz-nav-left">
			<div class="navbar-form navbar-left clearfix">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-search"></i></span>
						<input type="text" class="form-control input-sm" placeholder="Search">
					</div>
				</div>
				<button type="submit" class="btn btn-sm btn-default"><i class="fa fa-plus"></i> Add</button>
			</div>
		</span>
	</div>

	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse meyz-nav-collapse">
		<div class="navbar-form navbar-right meyz-nav-right">
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">Limit</span>
					<select name="" class="form-control input-sm"><option value="">Auto</option><option value="">20</option><option value="">30</option><option value="">All</option></select>
				</div>
				<!-- <input type="text" class="form-control" placeholder="Search"> -->
			</div>
		</div>
	</div><!-- /.navbar-collapse -->
</nav>
<hr />


<!-- NEW HEADER -->
<nav class="navbar navbar-default meyz-nav1" role="navigation">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".meyz-nav-collapse1">
			&nbsp;<i class="fa fa-angle-down"></i>&nbsp;
		</button>

		<span class="navbar-brand meyz-nav-left1">
			<div class="navbar-form">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-search"></i></span>
						<input type="text" class="form-control input-sm" placeholder="Search">
					</div>
				</div>
			</div>
		</span>
	</div>

	<div class="collapse navbar-collapse meyz-nav-collapse1">
		<ul class="nav navbar-nav navbar-right">
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Limit : Auto <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><a href="#"><i class="fa fa-angle-right"></i> Auto</a></li>
					<li><a href="#">20</a></li>
					<li><a href="#">30</a></li>
					<li><a href="#">40</a></li>
					<li><a href="#">All</a></li>
				</ul>
			</li>
			<li><a href="#"><i class="fa fa-plus"></i> New</a></li>
		</ul>
	</div><!-- /.navbar-collapse -->
</nav>


<!-- ALTERNATE HEADER -->
<nav class="navbar navbar-default meyz-nav2" role="navigation">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".meyz-nav-collapse2">
			&nbsp;<i class="fa fa-angle-down"></i>&nbsp;
		</button>

		<span class="navbar-brand meyz-nav-left2">
			<div class="navbar-form">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-search"></i></span>
						<input type="text" class="form-control input-sm" placeholder="Search">
					</div>
				</div>
			</div>
		</span>
	</div>

	<div class="collapse navbar-collapse meyz-nav-collapse2">
		<ul class="nav navbar-nav navbar-left">
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Limit : Auto <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><a href="#"><i class="fa fa-angle-right"></i> Auto</a></li>
					<li><a href="#">20</a></li>
					<li><a href="#">30</a></li>
					<li><a href="#">40</a></li>
					<li><a href="#">All</a></li>
				</ul>
			</li>
			<li><a href="#"><i class="fa fa-plus"></i> New</a></li>
		</ul>
	</div><!-- /.navbar-collapse -->
</nav>


<!-- 	<div class="row">
		<div class="col-sm-12">

			<div class="meyz-limit pull-left form-group">
				<div class="input-group">
					<span class="input-group-addon">Limit</span>
					<select name="" class="form-control input-sm"><option value="">Auto</option><option value="">20</option><option value="">30</option><option value="">All</option></select>
				</div>
			</div>
			<div class="col-xs-7 col-sm-5 col-lg-3 meyz-search pull-right form-group">
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-search"></i></span>
					<input type="text" name="" id="search" class="form-control input-sm" />
				</div>
				<button class="btn btn-default btn-sm"><i class="fa fa-plus"></i> Add</button>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div> -->


<!-- <div id="datatables" class="dtable "></div> -->
<!--<h3>Template</h3>
 <div class="dtable ">
	<div class="pheader">
		<span class="input-group" style="float:left; width:500px;">
			<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
			<input type="text" id="search" class="form-control" />
			<span class="input-group-addon" style="border-left:0px; border-right:0px;">Limit</span>
			<select id="limit" class="form-control"><option>10</option><option>20</option><option>30</option><option>40</option></select>
		</span>
		<button class="btn btn-default addnew" style="margin-left:5px; float:left;"> Add New </button>
		<div style="position:relative; float:left; width:38px; "><div class="sspin" id="gload" style="display: none;"></div>
		</div>
		<div style="clear:both"></div>
	</div>
	<div class="mytable box-outer-shadow" style="width:922;">
		<div id="tblheader">
			<div class="rowcell header sort" style="width: 330px; background-color: rgb(255, 255, 255);" id="fullname">Name
				<div style="background-position: -128px 0px;"></div>
			</div>
			<div class="rowcell header sort" style="width: 330px; background-color: rgba(0, 0, 0, 0.0196078);" id="shortname">Short name
				<div style="background-position: -64px -192px;"></div>
			</div>
			<div class="rowcell header sort" style="width: 70px; background-color: rgb(255, 255, 255);" id="starrating">Star<div style="background-position: -128px 0px;"></div></div>
			<div class="rowcell header sort" style="background-color: rgb(255, 255, 255);" id="totalrooms">Total Rooms<div style="background-position: -128px 0px;"></div></div>
			<div class="rowcell header " style="width:70px;" id="addss"> <div></div></div>
			<div style="clear:both"></div>
		</div>
		<span class="tblbody">
			<div id="item_4" style="width:922;" class="datarow">
				<div class="rowcell fullname" style="width:330px;text-align:left">Traders Hotel</div>
				<div class="rowcell shortname" style="width: 330px; text-align: left; background-color: rgba(0, 0, 0, 0.0196078);">traders-hotel-male</div>
				<div class="rowcell starrating" style="width:70px;">4</div>
				<div class="rowcell totalrooms" style="width:120px;">60</div>
				<div class="rowcell addss" style="width:70px; text-align:center; padding:3px;">
					<span class="btn-group dtbtn"><a rel="tab" href="hotels.php?action=edit&amp;itemID=4" id="itemd_4" class="edit btn "><i class="glyphicon glyphicon-pencil"></i></a>
						<span id="itemd_4" class="del btn "><i class="glyphicon glyphicon-remove"></i></span>
					</span>
				</div>
				<div style="clear:both;"></div>
			</div>
			<div id="item_1" style="width:922;" class="datarow">
				<div class="rowcell fullname" style="width:330px;text-align:left">Kurumba Maldives</div>
				<div class="rowcell shortname" style="width: 330px; text-align: left; background-color: rgba(0, 0, 0, 0.0196078);">kurumba</div>
				<div class="rowcell starrating" style="width:70px;">4</div>
				<div class="rowcell totalrooms" style="width:120px;">120</div>
				<div class="rowcell addss" style="width:70px; text-align:center; padding:3px;">
					<span class="btn-group dtbtn"><a rel="tab" href="hotels.php?action=edit&amp;itemID=1" id="itemd_1" class="edit btn "><i class="glyphicon glyphicon-pencil"></i></a>
						<span id="itemd_1" class="del btn "><i class="glyphicon glyphicon-remove"></i></span>
					</span>
				</div>
				<div style="clear:both;"></div>
			</div>
		</span>
	</div>
	<div id="tblfooter" style="width:922;">
		<div class="pignate">
			<div class="jquery-bootstrap-pagination">
				<ul class="pagination">
					<li class="disabled"><a data-page="0">&lt;</a></li>
					<li class="active"><a data-page="1">1</a></li>
					<li><a data-page="2">2</a></li>
					<li><a data-page="2">&gt;</a></li>
				</ul>
			</div>
		</div>
		<span class="btn-group norow norow" style="float:right;"><button class="btn btn-default disabled">#rows</button><button id="total" class="btn btn-default disabled">12</button></span>
		<div style="clear:both;"></div>
	</div>
</div> -->

</div>
</body>
</html>