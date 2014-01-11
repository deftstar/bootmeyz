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
<div class="container">

	<div class="bootmeyz">

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
		<div class="meyz-table">
			<div class="thead">
				<div class="th">Name</div>
				<div class="th">Address</div>
				<div class="th">test</div>
			</div>
			<div class="tbody">
				<div class="tr">
					<div class="td">qeqr</div>
					<div class="td">qwerq</div>
					<div class="td">adfadf</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>