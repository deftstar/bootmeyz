<?php 
//date_default_timezone_set('Indian/Maldives');
class hotacDB {

	function dbconnect(){
		$db = new mysqli('localhost', 'root', 'qyj3ewqo8j', 'hotac');
		if($db->connect_errno > 0){
		    die('Unable to connect to database [' . $db->connect_error . ']');
		}
		return $db;
	}
	// function dbconnect(){
	// 	$db = new mysqli('127.0.0.1', 'amindevn_amindev', 'aLL3in1one', 'amindevn_hotacDB');
	// 	if($db->connect_errno > 0){
	// 	    die('Unable to connect to database [' . $db->connect_error . ']');
	// 	}
	// 	return $db;
	// }
	// function quickproplookup($keyword){
	// 	$this->dbconnect();		
	// }

	function datatableview($term,$limit,$start,$order,$ordert,$type,$page){
		$this->dbconnect();
		if($page == 'property'){
			$aColumns = array( 'fullname','shortname' );
			$aFeilds = 'propId, fullname,shortname,starrating,totalrooms';
			$table = 'property';
		}else if ($page == 'country'){
			$table = 'country';
			$aColumns = array( 'fullname','shortname' );
			$aFeilds = 'countryId,fullname,shortname';
		}else if ($page == 'region'){
			$table = 'region';
			$aColumns = array( 'fullname','shortname' );
			$aFeilds = 'regionId,fullname,shortname';
		}else if ($page == 'city'){
			$table = 'city';
			$aColumns = array( 'fullname','shortname' );
			$aFeilds = 'cityId,fullname,shortname';
		}else if ($page == 'lmtrans'){
			$table = 'lmtrans';
			$aColumns = array( 'fullname','shortname' );
			$aFeilds = "lmtId,fullname,shortname, IF( lmtrans.type ='1', 'Transport','Landmarks') as type";
		}
		
		
		$sWhere = "";
		if($term != ""){
			$sWhere = "WHERE (";
			foreach ($aColumns as $i => $value) {
				$pieces = explode(" ", $term);
				$count = count($pieces);
				$search;
				if($count > 1){
					for($it=0; $it<$count; $it++){
						if($it == 0){
							$search = $aColumns[$i]." LIKE '%".$pieces[$it]."%'";
						}else{
							$search .= " AND ".$aColumns[$i]." LIKE '%".$pieces[$it]."%'";
						}
					}
				}else{
					$search = $aColumns[$i]." LIKE '%".$term."%'";
				}
				$sWhere .= $search." OR ";
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			//$sWhere .= ' AND status = 0 ';
			$sWhere .= ')';
		}else{
			$sWhere .= '';
		}

		if($type == 'count'){
			$query = "SELECT {$aFeilds} FROM {$table} {$sWhere}";
			$rs1=$this->dbconnect()->query($query);
			$results = $rs1->num_rows;	
			//var_dump(expression)	
		}else{
			$query = "SELECT {$aFeilds} FROM {$table} {$sWhere} order by {$order} {$ordert} limit $start,$limit";
				$rs=$this->dbconnect()->query($query);
				//var_dump($query);
				while($row = $rs->fetch_assoc()){
					$results[]=$row;
				}	
		}
		
		if(isset($results)) return $results; else return 'NoData';
	}
	// function installallcountires($country){
	// 	$sql = "INSERT INTO refcountry (cname) VALUES('$country')";	
	// 	$this->dbconnect()->query($sql);	

	// }
	function roomprice($prop_acId){
		$result = '';
		$today = date("Y-m-d");
		$sql="SELECT par.* FROM prop_accomadation_rates par 
		-- LEFT JOIN  prop_ac_ratedetails pard ON pard.rateId = par.rateId
		WHERE par.prop_acId ='$prop_acId' AND par.date_validto >= '$today' LIMIT 5";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$rateId = $row['rateId'];
			$sql1 = "SELECT * FROM prop_ac_ratedetails WHERE rateId ='$rateId'";
			$rs1=$this->dbconnect()->query($sql1);
			$ratedetails = '';
			while($row1 = $rs1->fetch_assoc()){
				$ratedetails[] = $row1;
			}
			$row['ratedetails'] = $ratedetails;
			$result[]=$row;
		}
		return $result;
	}

	function login($post){
		$email = $post['email'];
		$password = md5($post['password']);
		$sql="SELECT firstname,lastname,userId FROM tbluser WHERE emailaddress = '$email' AND password ='$password'";
		$rs=$this->dbconnect()->query($sql);
		if($row = $rs->fetch_assoc()){
			$result=$row;
		}else{
			$result = 'no';
		}
		return $result;

	}
	function getpropid($shortname){
		$sql="SELECT propId FROM property WHERE shortname ='$shortname'";
		$rs=$this->dbconnect()->query($sql);
		if($row = $rs->fetch_assoc()){
			return $row['propId'];
		}
	}
	function getallprop(){
		$sql="SELECT propId,fullname,shortname FROM property";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result[] = $row;
		}
		return $result;		
	}
	function getproprooms($propId){
		$sql="SELECT prop_acId,ac_name FROM prop_accomadations WHERE propId='$propId'";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result[] = $row;
		}
		return $result;		
	}
	function propdetail($propId){
		$sql = "SELECT p.propId,p.gpscordinates,p.maptype,p.shortname,p.fullname,p.starrating,p.regionId,p.cityId,p.fulldesc,p.address,p.ptId,pt.proptype,p.ptype,p.totalrooms,l.extra,c.fullname as country,c.shortname as countryshort,r.fullname as region,r.shortname as regionshort,ct.fullname as city,ct.shortname as cityshort 
			FROM property p
			LEFT JOIN country c ON
			p.countryId = c.countryId
			LEFT JOIN region r ON
			p.regionId = r.regionId
			LEFT JOIN city ct ON
			p.cityId = ct.cityId
			LEFT JOIN prop_type pt ON
			p.ptId = pt.ptId
			LEFT JOIN lookuptable l ON
			l.shortname = '$propId'
			 WHERE p.shortname = '$propId' OR p.propId = '$propId'";
		$rs=$this->dbconnect()->query($sql);
		if($rs === false) {
		  //trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
			return 'Ooops!!! No luck';
		} else {
			$result = '';
		  	$rs->data_seek(0);
			while($row = $rs->fetch_assoc()){
				$result=$row;
			}
			return $result;
		}
	}

	function updatecountry($id,$post){
		$fulldesc = $this->dbconnect()->real_escape_string($post['fulldesc']);
		$fullname=$post['fullname'];
		$shortname= $post['shortname'];
		$sql="UPDATE country SET fullname='$fullname',shortname='$shortname',summary_desc='$fulldesc' WHERE countryId = '$id'";
		if($this->dbconnect()->query($sql) === false) {
			return 'Error / could be shortname(it has to be unique)';
		}else{
			return 'success';
		}
	}
	function updateregion($id,$post){
		$fulldesc = $this->dbconnect()->real_escape_string($post['fulldesc']);
		$fullname=$post['fullname'];
		$shortname= $post['shortname'];
		$countryId= $post['countryId'];
		$sql="UPDATE region SET fullname='$fullname',shortname='$shortname',summary_desc='$fulldesc',countryId='$countryId' WHERE regionId = '$id'";
		if($this->dbconnect()->query($sql) === false) {
			return 'Error / could be shortname(it has to be unique)';
		}else{
			return 'success';
		}
	}
	function addlmt($post){
		$fulldesc = $this->dbconnect()->real_escape_string($post['fulldesc']);
		$fullname=$post['fullname'];
		$shortname= $post['shortname'];
		$countryId= $post['countryId'];
		$type= $post['type'];
		$sql="INSERT INTO lmtrans (fullname, shortname, ldesc, countryId,type)  VALUES ('$fullname','$shortname','$fulldesc','$countryId','$type')";
		if($this->dbconnect()->query($sql) === false) {
			return 'Error / could be shortname(it has to be unique)';
		}else{
			return 'success';
		}
	}
	function updatelmt($id,$post){
		$fulldesc = $this->dbconnect()->real_escape_string($post['fulldesc']);
		$fullname=$post['fullname'];
		$shortname= $post['shortname'];
		$countryId= $post['countryId'];
		$type= $post['type'];
		$sql="UPDATE lmtrans SET fullname='$fullname',shortname='$shortname',ldesc='$fulldesc',countryId='$countryId',type='$type' WHERE lmtId = '$id'";
		if($this->dbconnect()->query($sql) === false) {
			return 'Error / could be shortname(it has to be unique)';
		}else{
			return 'success';
		}
	}
	function addregion($post){
		$fulldesc = $this->dbconnect()->real_escape_string($post['fulldesc']);
		$fullname=$post['fullname'];
		$shortname= $post['shortname'];
		$countryId= $post['countryId'];
		$sql="INSERT INTO region  (fullname, shortname, summary_desc, countryId)  VALUES ('$fullname','$shortname','$fulldesc','$countryId')";
		if($this->dbconnect()->query($sql) === false) {
			return 'Error / could be shortname(it has to be unique)';
		}else{
			return 'success';
		}
	}
	function updatecity($id,$post){
		//var_dump($post);
		$fulldesc = $this->dbconnect()->real_escape_string($post['fulldesc']);
		$fullname = $this->dbconnect()->real_escape_string($post['fullname']);
		//$fullname=$post['fullname'];
		$shortname= $post['shortname'];
		$countryId= $post['countryId'];
		$regionId= $post['regionId'];		
		$sql="UPDATE city SET fullname='$fullname',shortname='$shortname',summary_desc='$fulldesc',countryId='$countryId',regionId='$regionId' WHERE cityId = '$id'";
	//	echo $sql;
		if($this->dbconnect()->query($sql) === false) {
			return 'Error / could be shortname(it has to be unique)';
		}else{
			return 'success';
		}
	}
	function addcity($post){
		$fulldesc = $this->dbconnect()->real_escape_string($post['fulldesc']);
		$fullname=$post['fullname'];
		$shortname= $post['shortname'];
		$countryId= $post['countryId'];
		$regionId= $post['regionId'];
		$sql="INSERT INTO city  (fullname, shortname, summary_desc, countryId,regionId)  VALUES ('$fullname','$shortname','$fulldesc','$countryId','$regionId')";
		if($this->dbconnect()->query($sql) === false) {
			return 'Error / could be shortname(it has to be unique)';
		}else{
			return 'success';
		}
	}
	function updatehoteldetail($id,$post){
		//var_dump($id);
		$detail_content = $this->dbconnect()->real_escape_string($post['detail_content']);
		$heading=$post['heading'];
		$sql = "UPDATE prop_detail SET detail_content='$detail_content', heading='$heading' WHERE propdId = '$id'";
		// $sql="UPDATE region SET fullname='$fullname',shortname='$shortname',summary_desc='$fulldesc' WHERE regionId = '$id'";
		if($this->dbconnect()->query($sql) === false) {
			return 'Error';
		}else {
			return 'success';
		}
	}
	function deldetail($id){
		$sql = "DELETE FROM prop_detail WHERE propdId= '$id'";
		$this->dbconnect()->query($sql);
	}
	function addhoteldetail($id,$post){
		//var_dump($post);
		$detail_content = $this->dbconnect()->real_escape_string($post['detail_content']);
		$heading=$post['heading'];
		$sql = "INSERT INTO prop_detail (detail_content, heading, propId) 
				VALUES ('$detail_content', '$heading', '$id')";
		// $sql="UPDATE region SET fullname='$fullname',shortname='$shortname',summary_desc='$fulldesc' WHERE regionId = '$id'";
		if($this->dbconnect()->query($sql) === false) {
			trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $this->dbconnect()->error, E_USER_ERROR);;
		}else {
			return 'success';
		}
	}
	function addrooms($id,$post){
		//var_dump($id);
		$details = $this->dbconnect()->real_escape_string($post['fulldesc']);
		$ac_name=$post['ac_name'];
		$no_rooms= $post['no_room'];
		$max_adult_bed= $post['max_adult_bed'];
		$max_child_bed= $post['max_child_bed'];
		$sql="INSERT INTO prop_accomadations (ac_name,no_rooms,max_adult,max_child,details,propId) VALUES ('$ac_name','$no_rooms','$max_adult_bed','$max_child_bed','$details','$id')";
		if($this->dbconnect()->query($sql) === false) {
			return 'Error';
		}else{
			return 'success';
		}
	}
	function addroomprice($id,$post){
		//var_dump($id);
		//$prop_acId=$post['prop_acId'];
		$child_price= $post['child_price'];
		$infent_price= $post['infent_price'];
		$date_validfrom= $post['validfrom'];
		$date_validto= $post['validto'];
		$conn = $this->dbconnect();
		$sql="INSERT INTO prop_accomadation_rates (prop_acId,child_price,infent_price,date_validfrom,date_validto) VALUES ('$id','$child_price','$infent_price','$date_validfrom','$date_validto')";
		if($conn->query($sql) === true){
			$sql2 = "SELECT availableto FROM prop_accomadations WHERE prop_acId = '$id'";
			$rs=$this->dbconnect()->query($sql2);
			if($row = $rs->fetch_assoc()){
				//var_dump($row);
				// if($row['availablefrom'] == null) { 
				// 	$updatefrom = "UPDATE prop_accomadations SET availablefrom = '$date_validfrom' WHERE prop_acID = '$id'";
				// 	$this->dbconnect()->query($updatefrom);
				// }
				if($row['availableto'] == null || $row['availableto'] <= $date_validto){
					$updateto = "UPDATE prop_accomadations SET availableto = '$date_validto' WHERE prop_acID = '$id'";
					$this->dbconnect()->query($updateto);
				}

				// $newavailableto = $date_validto;
				
			}
			$rateId = $conn->insert_id;
			foreach ($post['adult_price'] as $key => $value) {
				$sql1 = "INSERT INTO prop_ac_ratedetails (rateId,no_adult,price) VALUES ('$rateId','$key','$value')";
				if($this->dbconnect()->query($sql1) === true) {
				}
			}
		}
	}

	function updateavailabledate($prop_acId){
		$sql = "SELECT date_validto FROM prop_accomadation_rates WHERE prop_acId = '$prop_acId' ORDER BY date_validto DESC";
		$rs=$this->dbconnect()->query($sql);
		if($row = $rs->fetch_assoc()){
			$result=$row['date_validto'];
		}
		return $result;	

	}

	function deleteroom($id){
		$conn = $this->dbconnect();
		$sql = "DELETE FROM prop_accomadations where prop_acId = '$id'";
		$sql1 = "DELETE FROM prop_accomadation_rates where prop_acId = '$id'";
		if($conn->query($sql) === true){
			if($conn->query($sql1) === true){
				$delid = $conn->affected_rows;
				$sql3 = "DELETE FROM prop_ac_ratedetails WHERE rateId='$delid'";
				$conn->query($sql3);
			}
		};
		//$this->dbconnect()->query($sql1);		
	}

	function deleteroomprice($id){
		$sql = "DELETE FROM prop_accomadation_rates where rateId = '$id'";
		$sql1 = "DELETE FROM prop_ac_ratedetails where rateId = '$id'";
		$this->dbconnect()->query($sql);
		$this->dbconnect()->query($sql1);

	}

	function updateroombasic($id,$post){
		$details = $this->dbconnect()->real_escape_string($post['fulldesc']);
		$ac_name=$post['ac_name'];
		$no_rooms= $post['no_room'];
		$max_adult_bed= $post['max_adult_bed'];
		$max_child_bed= $post['max_child_bed'];
		$sql="UPDATE prop_accomadations SET ac_name='$ac_name',no_rooms='$no_rooms',max_adult='$max_adult_bed',max_child='$max_child_bed',details='$details' WHERE prop_acId = '$id'";
		if($this->dbconnect()->query($sql) === false) {
			return 'Error';
		}else{
			

			return 'success';
		}
	}
	function addhotelbasic($post){
		$fullname = $post["fullname"];
		$shortname = $post["shortname"];
		$starrating = $post["starrating"];
		$ptype = $post["ptype"];
		$ptId = $post["ptId"];
		$fulldesc = $this->dbconnect()->real_escape_string($post['fulldesc']);
		$address = $post["address"];
		$countryId = $post["countryId"];
		$regionId = $post["regionId"];
		$cityId = $post["cityId"];
		$totalrooms = $post["totalrooms"];
		$sql = "INSERT INTO property (fullname,shortname,starrating,ptype,ptId,fulldesc,address,countryId,regionId,cityId,totalrooms)
				VALUES ('$fullname','$shortname','$starrating','$ptype','$ptId','$fulldesc','$address','$countryId','$regionId','$cityId','$totalrooms')";
		if($this->dbconnect()->query($sql) === false) {
			//return 'Error';
			trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $this->dbconnect()->error, E_USER_ERROR);
		}else{
			return 'success';
		}	
	}

	function updatehotelbasic($id,$post){
		$details = $this->dbconnect()->real_escape_string($post['fulldesc']);
		$fullname=$post['fullname'];
		$shortname= $post['shortname'];
		$ptId= $post['ptId'];
		$ptype = $post['ptype'];
		$starrating= $post['starrating'];
		$address = $post['address'];
		$countryId = $post['countryId'];
		$regionId = $post['regionId'];
		$cityId = $post['cityId'];
		$gpscordinates = $post['gpscordinates'];
		$maptype = $post['maptype'];
		$sql="UPDATE property SET fullname='$fullname',shortname='$shortname',maptype='$maptype',gpscordinates='$gpscordinates',starrating='$starrating',ptype='$ptype',ptId='$ptId',fulldesc='$details',address='$address',countryId='$countryId',regionId='$regionId',cityId='$cityId' WHERE propId = '$id'";
		if($this->dbconnect()->query($sql) === false) {
			//return 'Error';
			trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $this->dbconnect()->error, E_USER_ERROR);
		}else{
			return 'success';
		}
	}

	function addhdetails($post){
		$propId = $post['id']; $heading=$post['heading']; $detail_content = $post['detail_content'];
		$sql = "INSERT INTO prop_detail (propId,heading,detail_content) VALUES ('$proId','$heading','$detail_content')";
		if($this->dbconnect()->query($sql) === false) {
			trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $this->dbconnect()->error, E_USER_ERROR);
		}else{
			return 'success';
		}		
	}
	function delLocation($table,$feild,$id){
		$sql="DELETE FROM {$table} WHERE {$feild} = '$id'";
		$this->dbconnect()->query($sql);
	}	
	function getlocationdetails($table,$feild,$id){
		$sql = "SELECT * FROM {$table} WHERE {$feild} = '$id'";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result=$row;
		}
		return $result;		
	}
	function getallcountries(){
		$sql = "SELECT * FROM country";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result[]=$row;
		}
		return $result;		
	}
	function getallregions(){
		$sql = "SELECT * FROM region";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result[]=$row;
		}
		return $result;		
	}	

	function getallcities(){
		$sql = "SELECT * FROM city";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result[]=$row;
		}
		return $result;		
	}
	function getalltype(){
		$sql = "SELECT * FROM prop_type";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result[]=$row;
		}
		return $result;		
	}	
	function detailpropinfo($propId){
		$result = '';
		$sql = "SELECT * FROM prop_detail WHERE propId = '$propId'";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result[]=$row;
		}
		return $result;
	}
	function getdetailinfo($propdId){
		$result = '';
		$sql = "SELECT * FROM prop_detail WHERE propdId = '$propdId'";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result=$row;
		}
		return $result;
	}
	function getlmtrans($type){
		$sql = "SELECT * FROM lmtrans WHERE type = '$type'";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result[]=$row;
		}
		return $result;		
	}
	function addtransfer($post,$propId){
		$dfromlmt = $post['dfromlmt']; $lmtId = $post['lmtId'];
		$sql = "INSERT INTO prop_nearby (propId,lmtId,dfromlmt) VALUES('$propId','$lmtId','$dfromlmt')";
		$this->dbconnect()->query($sql);
	}
	function addtmethodrate($post,$lmtnId){
		//$lmtnId = $post['lmtnId'];
		$tftId = $post['tftId'];
		$tduration = $post['tduration'];
		$adult_price = $post['adult_price'];
		$child_price = $post['child_price'];
		$sql = "INSERT INTO nearby_transfer_rate(lmtnId,tftId,tduration,adult_price,child_price) VALUES ('$lmtnId','$tftId','$tduration','$adult_price','$child_price')";
		$this->dbconnect()->query($sql);
	}
	function gettmethods(){
		$sql = "SELECT * FROM transfertypes";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result[]=$row;
		}
		return $result;		
	}
	function gettransferprice($ntrId,$nopeople){
		$people = explode('|', $nopeople);
		$adults = $people[0]; $childs = $people[1];
		$sql = "SELECT '$adults' * ntr.adult_price+'$childs' * ntr.child_price as price,tt.ttype FROM nearby_transfer_rate ntr 
		LEFT JOIN transfertypes tt ON ntr.tftId = tt.tftId WHERE ntr.ntrId = '$ntrId'";
		$rs=$this->dbconnect()->query($sql);
		if($row = $rs->fetch_assoc()){
			return $row;
		}
	}
	function removelmt($lmtnId){
		$sql = "DELETE FROM prop_nearby WHERE lmtnId = '$lmtnId'";
		$sql1 = "DELETE FROM nearby_transfer_rate WHERE lmtnId = '$lmtnId'";
		$this->dbconnect()->query($sql);
		$this->dbconnect()->query($sql1);
	}
	function getnearbytrans($propId){
		$result = '';
		$sql = "SELECT  n.*,lmt.* FROM prop_nearby n INNER JOIN lmtrans lmt on n.lmtId = lmt.lmtId WHERE n.propId = '$propId' AND lmt.type = 1";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result[]=$row;
		}
		return $result;		
	}
	function getnearbylandmarks($propId){
		$result = '';
		$sql = "SELECT  n.*,lmt.* FROM prop_nearby n INNER JOIN lmtrans lmt on n.lmtId = lmt.lmtId WHERE n.propId = '$propId' AND lmt.type = 2";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result[]=$row;
		}
		return $result;		
	}
	function getamethods($lmtnId){
		$sql = "SELECT ntr.*,tt.ttype FROM nearby_transfer_rate ntr LEFT JOIN transfertypes tt ON ntr.tftId = tt.tftId WHERE ntr.lmtnId = '$lmtnId'";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result[]=$row;
		}
		return $result;		
	}
	function deltmethod($ntrId){
		$sql = "DELETE FROM nearby_transfer_rate WHERE ntrId = '$ntrId'";
		$this->dbconnect()->query($sql);
	}


	function getlandmark($id){
		$sql = "SELECT * FROM lmtrans WHERE lmtId = '$id' OR shortname ='$id'";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result=$row;
		}
		return $result;		
	}

	function getlocdetails($loc){
		//check type of location from lookuptable
		$checktype = "SELECT ltype FROM lookuptable WHERE shortname = '$loc'";
		$rs=$this->dbconnect()->query($checktype);
		if($row = $rs->fetch_assoc()){ $type = $row['ltype']; }
		$result = '';
		if($type == 2){
			$detail = "SELECT countryId as id, fullname, summary_desc, '2' as ltype, shortname FROM country WHERE shortname = '$loc'";
		}else if($type == 3){
			$detail = "SELECT regionId as id, fullname, summary_desc, '3' as ltype, shortname FROM region WHERE shortname = '$loc'";			
		}else if($type == 4){
			$detail = "SELECT cityId as id, fullname, summary_desc, '4' as ltype, shortname FROM city WHERE shortname = '$loc'";
		}
		$rs=$this->dbconnect()->query($detail);
		if($rs = $rs->fetch_assoc()){
			$result = $rs;
		}
		return $result;
	}



	function hotelsin($id,$type,$filter='p.fullname',$forder='ASC',$start=0){
		if($type == 2){
			$feild = 'p.countryId';
		}else if($type == 3){
			$feild = 'p.regionId';
		}else{
			$feild = 'p.cityId';
		}
		// if($filter != '') $filter = 'p.fullname'; else $filter 'p'.$filter;
		// if($forder != '') $forder = 'DESC'; 
		$result = '';
		$hotels = "SELECT p.propId,pt.proptype,p.ptype,p.totalrooms,p.shortname,p.fullname,p.starrating,p.regionId,p.address,c.fullname as country,c.shortname as countryshort,r.fullname as region,r.shortname as regionshort,ct.fullname as city,ct.shortname as cityshort FROM property p
			LEFT JOIN country c ON
			p.countryId = c.countryId
			LEFT JOIN region r ON
			p.regionId = r.regionId
			LEFT JOIN city ct ON
			p.cityId = ct.cityId
			LEFT JOIN prop_type pt ON
			p.ptId = pt.ptId
			 WHERE {$feild} = '$id'
			 ORDER BY {$filter} {$forder}
			 LIMIT $start, 6";
		$gettotal = "SELECT * FROM property p  WHERE {$feild} = '$id'";
		$rs=$this->dbconnect()->query($hotels);
		$rs1=$this->dbconnect()->query($gettotal);
		$TotalRcount = $rs1->num_rows;
		$rs->data_seek(0);
		while($row = $rs->fetch_assoc()){
			$result['hotels'][] = $row;
		}
		$result['total'] = $TotalRcount;
		return $result;

	}

	function hotelsnear($id,$filter='pn.dfromlmt',$forder='ASC',$start=0){
		$result = '';
		$hotels = "SELECT pn.dfromlmt,p.propId,pt.proptype,p.ptype,p.totalrooms,p.shortname,p.fullname,p.starrating,p.regionId,p.address,c.fullname as country,c.shortname as countryshort,r.fullname as region,r.shortname as regionshort,ct.fullname as city,ct.shortname as cityshort FROM prop_nearby pn
				LEFT JOIN property p ON pn.propId = p.propId
				LEFT JOIN country c ON p.countryId = c.countryId
				LEFT JOIN region r ON p.regionId = r.regionId
				LEFT JOIN city ct ON p.cityId = ct.cityId
				LEFT JOIN prop_type pt ON p.ptId = pt.ptId
				WHERE pn.lmtId = '$id'
				ORDER BY {$filter} {$forder}
				LIMIT $start, 6";
		$gettotal = "SELECT * FROM prop_nearby pn  WHERE pn.lmtId = '$id'";
		$rs=$this->dbconnect()->query($hotels);
		$rs1=$this->dbconnect()->query($gettotal);
		$TotalRcount = $rs1->num_rows;
		$rs->data_seek(0);
		while($row = $rs->fetch_assoc()){
			$result['hotels'][] = $row;
		}
		$result['total'] = $TotalRcount;
		return $result;

	}

	function getreviews($propId,$traveling="0",$start=0){
		$result = '';
		if($traveling == 0){ $filter = ''; }else{ $filter = 'AND traveling='.$traveling; }
		$reviews = "SELECT rw.*,u.firstname,u.lastname,c.fullname,u.profilepic FROM user_reviews rw
		INNER JOIN tbluser u
		ON rw.userId = u.userId
		INNER JOIN country c
		ON u.countryId = c.countryId
		WHERE rw.propId = '$propId' AND moderated = 1 {$filter}
		ORDER BY rw.cdate DESC
		LIMIT $start,3";
		$gettotal = "SELECT rwId FROM user_reviews WHERE propId='$propId' AND moderated = 1 {$filter}";
		$rs1=$this->dbconnect()->query($gettotal);
		$TotalRcount = $rs1->num_rows;
		$rs=$this->dbconnect()->query($reviews);
		$rs->data_seek(0);
		while($row = $rs->fetch_assoc()){
			$result['reviews'][] = $row;
		}
		$result['total'] = $TotalRcount;
		return $result;
	}

	function avgrating($propId,$traveling){
		if($traveling == 0){ $filter = ''; }else{ $filter = 'AND traveling='.$traveling; }
		$avg = "SELECT count(rwId) as total,  AVG(rating) as avgrating, AVG(rcleanliness) as avgrcleanliness, AVG(rservice) as avgrservice,AVG(rcomfort) as avgrcomfort,AVG(rneighbourhood) as avgrneighbourhood,AVG(rcondition) as avgrcondition
			FROM user_reviews WHERE propId='$propId' {$filter}";
		$rs=$this->dbconnect()->query($avg);
		$rs->data_seek(0);
		if($row = $rs->fetch_assoc()){
			return $row;
		}else{
			return false;
		}
	}

	function addreviews($post,$propId,$userId){
		$summarytitle = $_POST['short-summary'];
		$review = $_POST['review'];
		$traveling = $_POST['traveling'];
		$rating = $_POST['rating'];
		$rcleanliness = $_POST['rcleanliness'];
		$rservice = $_POST['rservice'];
		$rcomfort = $_POST['rcomfort'];
		$rcondition = $_POST['rcondition'];
		$rneighbourhood = $_POST['rneighbourhood'];
		$addreviews = "INSERT INTO user_reviews (summarytitle,review,traveling,rating,rcleanliness,rservice,rcomfort,rcondition,rneighbourhood,propId,userId)
				VALUES ('$summarytitle','$review','$traveling','$rating','$rcleanliness','$rservice','$rcomfort','$rcondition','$rneighbourhood','$propId','$userId')";
		$this->dbconnect()->query($addreviews);
		return 'Thankyou for your review.';	
	}
	function getmealprice($mealId,$nopeople){
		$people = explode('|', $nopeople);
		$adults = $people[0]; $childs = $people[1];
		$sql = "SELECT '$adults' * pmg.adult_price+'$childs' * pmg.child_price as price,m.mName FROM prop_meal_package pmg 
		LEFT JOIN mealpacks m ON pmg.pack_name = m.mpId WHERE pmg.propmealId = '$mealId'";
		$rs=$this->dbconnect()->query($sql);
		if($row = $rs->fetch_assoc()){
			return $row;
		}
	}
	function getmealoptions($propId){
		$sql = "SELECT pmg.*,m.mName  FROM prop_meal_package pmg 
		LEFT JOIN mealpacks m ON pmg.pack_name = m.mpId
		WHERE pmg.propId = '$propId' ORDER BY pmg.adult_price";
		$rs=$this->dbconnect()->query($sql);
		if($rs === false) {
			return 'Ooops!!! No luck';
		} else {
			$result = '';
		  	$rs->data_seek(0);
			while($row = $rs->fetch_assoc()){
				$result[]=$row;
			}
			return $result;
		}		
	}
	function delmeal($propmealId){
		$sql="DELETE FROM prop_meal_package WHERE propmealId = '$propmealId'";
		$this->dbconnect()->query($sql);
	}
	function addmeal($post,$propId){
		var_dump('test');
		$packname = $post['mpId']; $adult_price = $post['adult_price'];$child_price = $post['child_price'];
		$sql="INSERT INTO prop_meal_package (propId,pack_name,adult_price,child_price) VALUES ('$propId','$packname','$adult_price','$child_price')";
		$this->dbconnect()->query($sql);
	}
	function getallmeals(){
		$sql="SELECT * from mealpacks";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result[]=$row;
		}
		return $result;		
	}
	function getrooms($propId){
		$result = '';
		$sql = "SELECT * FROM prop_accomadations WHERE propId = '$propId'";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result[]=$row;
		}
		return $result;
	}

	function getroomdetails($prop_acId){
		$sql = "SELECT * FROM prop_accomadations WHERE prop_acId = '$prop_acId'";
		$rs=$this->dbconnect()->query($sql);
		while($row = $rs->fetch_assoc()){
			$result=$row;
		}
		return $result;		
	}
	function makebooking($post,$selected,$searched,$propId,$userId){
		$roomsd = explode(',', $selected['selectedrooms']);
		$checkindate = $searched['checkindate'];
		$checkoutdate = $searched['checkoutdate'];
		$aflight = $post["aflight"];
		$adate = $post["adate"];
		$dflight = $post["dflight"];
		$ddate = $post["ddate"];
		$binfo = $this->getinfoforbooking($selected,$searched);
		$conn = $this->dbconnect();
		// if($conn->query($sql) === true) {
		// 	//echo 'success stage 1';
		// 	$rateId = $conn->insert_id;
		// }
		$addbooking = "INSERT INTO bookings (userId,propId,checkindate,checkoutdate,aflight,adate,dflight,ddate) 
						VALUES ('$userId','$propId','$checkindate','$checkoutdate','$aflight','$adate','$dflight','$ddate')";
		if($conn->query($addbooking) === true) {
		$bookId = $conn->insert_id;
		}
		//echo $bookId;
		foreach ($binfo['roomdetails'] as $key => $rooms) {
			$fullname = $post['fullname'][$key];
			$rd = explode('|', $roomsd[$key]);
			$prop_acId = $rd[0];
			$remarks = $post['remarks'][$key];
			$nochild = 0; $noinfent = 0;
			$noadult = $searched['adult'][$key];
			if($searched['child'][$key] == "0"){
				$num_child = 0;
			}else{
				$num_child = count($searched['child'][$key]);
				foreach($searched['child'][$key] as $age){
					if($age < 2){
						$noinfent += 1;
					}else{
						$nochild += 1;
					}
				}
			}
			$bookingroom ="INSERT INTO booking_rooms (bookId,prop_acId,no_adults,no_childs,no_infents,fullname,remarks)
								VALUES ('$bookId','$prop_acId','$noadult','$nochild','$noinfent','$fullname','$remarks')";
			if($conn->query($bookingroom) === true) {
				$brId = $conn->insert_id;
				$update = "UPDATE prop_accomadations SET no_rooms = no_rooms - 1 WHERE prop_acId = '$prop_acId'";
				$conn->query($update);
			}
			foreach ($rooms[0]['rates'] as $nkey => $rate) {
				$bprice = $rate['daytotal'];
				$ddate = $rate['ddate'];
				if(isset($rate['newdaytotal'])) $newdaytotal = $rate['newdaytotal']; else $newdaytotal = '';
			 	$bookingroomrates = "INSERT INTO booking_room_rates (brId,bprice,bdprice,bdate) VALUES ('$brId','$bprice','$newdaytotal','$ddate')";
			 	$this->dbconnect()->query($bookingroomrates);
			} 
		}
		// if($selected['mealplane'] != ''){
		// 	$this->getmealprice();
		// }
	}
	function getinfoforbooking($selected,$searched){
		$rooms = explode(',', $selected['selectedrooms']);
		$propd = explode('|', $_SESSION['bookinfo']['info']);

		foreach ($rooms as $key => $room) {
			$adult = $searched["adult"][$key];
			if($searched['child'][$key] == "0"){
				$num_child = 0;
			}else{
				$num_child = count($searched['child'][$key]);
			} 
			$rd = explode('|', $rooms[$key]);
			$propId = $propd[0];
			$begin = new DateTime($searched['checkindate']);
			$end = new DateTime($searched['checkoutdate']);
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end);
			//$result[] = $rd[1];
			$prop_acId = $rd[0];
			$daystotal = 0; $ndaystotal = 0;
			foreach ( $period as $dt ){

				$ddate = $dt->format("Y-m-d");
				$getpricesforeachdate = "SELECT pacr.rateId,pacr.child_price,pacrd.price,pacr.infent_price,'$ddate' as ddate, pacrd.no_adult FROM prop_accomadation_rates pacr
				LEFT JOIN prop_ac_ratedetails pacrd
				ON pacr.rateId = pacrd.rateId
				WHERE pacr.prop_acId = '$prop_acId' AND pacrd.no_adult = '$adult' AND ('$ddate' BETWEEN pacr.date_validfrom AND pacr.date_validto)";			
					$rs1=$this->dbconnect()->query($getpricesforeachdate);
					$rs1->data_seek(0);
					if($row1 = $rs1->fetch_assoc()){
						$childprice = 0;
						if($num_child != 0){
							foreach($searched['child'][$key] as $age){
								//var_dump($age);
								if($age < 2){
									$childprice += (float)$row1['infent_price'];
								}else{
									$childprice += (float)$row1['child_price'];
								}
							}
						}
						$checkfordeals = "SELECT pd.dealamount,  pd.dealtype,pd.booksdate,pd.bookedate FROM prop_deals pd
							LEFT JOIN prop_deals_appliedrooms pdar ON pd.dealId = pdar.dealId
							WHERE ('$ddate' BETWEEN pd.dealsdate AND pd.dealedate) AND  pd.propId='$propId' AND pdar.prop_acId='$prop_acId' ";
						$rs2=$this->dbconnect()->query($checkfordeals);
						$rs2->data_seek(0);
						$dtotal = $row1['price'] + $childprice;
						$newdaystotal = 0;
						if($row2 = $rs2->fetch_assoc()){
							$bstatus = 0;
							if($row2['booksdate'] != null){
								$now = time();
								if(strtotime($row2['booksdate']) <= $now && strtotime($row2['bookedate']) >= $now) {
								    $bstatus = 1;
								}
							}else{	$bstatus = 1;	}
							if($bstatus == 1){
								if($row2['dealtype'] == 1) { $newdaystotal = $dtotal-($dtotal*($row2['dealamount']/100)); }
								else {  $newdaystotal = $dtotal-$row2['dealamount'];  }
								$row1['newdaytotal'] = $newdaystotal;
							}
							//var_dump($row2);
						}
						if($newdaystotal == 0) $ndaystotal += $dtotal; else $ndaystotal += $newdaystotal;
						$daystotal += $dtotal;
						$row1['daytotal'] = $dtotal;
						$tt[] = $row1;
						
					}

			}
					$result['info']['ntotal'] = $ndaystotal;
					$result['info']['total'] = $daystotal;
					$result['rates'] = $tt;
					$tt ='';
					$fullresults[] = $result;			
			$fullresults['roomname'] = $rd[1];
			$roomdetails[$key]= $fullresults;
			$fullresults = '';
		}
		$test['roomdetails'] = $roomdetails;
		return $test;
	}
	function getbookingoptions($query,$propId=NULL){
		$error = 0 ;
		$fullresults = '';
		if($propId === NULL){
			$shortname_type = explode("||", $query['shortname_type']) ;
			$propId =  $this->getpropid($shortname_type[0]);
		}
		if(!isset($propId)) $error = 1;
		if($error != 1){
			$result = array(
					'info'=>array(),
					'rates'=>array()
			);
			$no_rooms =$query['rooms'];
			$checkindate = $query['checkindate'];
			$checkoutdate = $query['checkoutdate'];
			$no_adults = array_map(function($details) {
				  return $details;
				}, $query['adult']);
			$min_adult = max($no_adults);
			$no_child = array_map(function($details) {
				  return $details;
				}, $query['child']);
			$min_child = max($no_adults);
			$no_rooms =$query['rooms'];
			$getallroomtypes = "SELECT pac.prop_acId,pac.no_rooms FROM prop_accomadations pac
				WHERE  pac.no_rooms >= 1 AND pac.propId = '$propId' ";
			$rs1=$this->dbconnect()->query($getallroomtypes);
			$rs1->data_seek(0);
			while($row1 = $rs1->fetch_assoc()){
				$amount_available[] = $row1;
			};
			// needto check if totall available rooms is greater or equal to whats needed ** important..
			foreach($query['adult'] as $key => $adult){
				if($query['child'][$key] == "0"){
					$num_child = 0;
				}else{
					$num_child = count($query['child'][$key]);
				} 
				$today = date("Y-m-d");
				$totalp = $adult + $num_child;
				$checkifrooms = "SELECT pac.prop_acId,pac.ac_name,pac.no_rooms,(pac.max_adult + pac.max_child) as totalp, pac.max_adult, pac.max_child FROM prop_accomadations pac
				INNER JOIN prop_accomadation_rates pacr
				ON pac.prop_acId = pacr.prop_acId
				WHERE pac.max_adult >= '$adult'  AND pac.no_rooms >= 1  AND pac.propId = '$propId'
				AND ('$today' <= '$checkindate' AND pac.availableto >= '$checkoutdate')
				GROUP BY pac.prop_acId
				HAVING  totalp >= '$totalp'";
				$rs=$this->dbconnect()->query($checkifrooms);
				$rs->data_seek(0);
					//var_dump($num_child);
				while($row = $rs->fetch_assoc()){
					$error = 0;
					$prop_acId = $row['prop_acId'];
					$getfirstthumb = "SELECT img_name FROM prop_gallery WHERE propId = '$prop_acId' AND type = 2";
					$rs01=$this->dbconnect()->query($getfirstthumb);
					if($row01 = $rs01->fetch_assoc()){ $thumb = $row01['img_name']; }else {$thumb = '';}
					//var_dump($row['totalp']);
					$row['img_name'] = $thumb;
					$nofinfent=0; $nofchild=0;
					if($num_child != 0){
						foreach($query['child'][$key] as $age){
							//var_dump($age);
							if($age < 2){
								$nofinfent += 1;
							}else{
								$nofchild += 1;
							}
						}
					}
					$row['nofchild'] = $nofchild; $row['nofinfent'] = $nofinfent;$row['nofadult'] = $adult;

					
					$begin = new DateTime($checkindate);
					$end = new DateTime($checkoutdate);

					$interval = DateInterval::createFromDateString('1 day');
					$period = new DatePeriod($begin, $interval, $end);
					
					$daystotal = 0; $ndaystotal = 0;
					foreach ( $period as $dt ){
						$ddate = $dt->format("Y-m-d");
						$getpricesforeachdate = "SELECT pacr.rateId,pacr.child_price,pacrd.price,pacr.infent_price,'$ddate' as ddate, pacrd.no_adult FROM prop_accomadation_rates pacr
						LEFT JOIN prop_ac_ratedetails pacrd
						ON pacr.rateId = pacrd.rateId
						WHERE pacr.prop_acId = '$prop_acId' AND pacrd.no_adult = '$adult' AND ('$ddate' BETWEEN pacr.date_validfrom AND pacr.date_validto)";
						$rs1=$this->dbconnect()->query($getpricesforeachdate);
						$rs1->data_seek(0);
						if($row1 = $rs1->fetch_assoc()){
							$childprice = 0;
							if($num_child != 0){
								foreach($query['child'][$key] as $age){
									//var_dump($age);
									if($age < 2){
										$childprice += (float)$row1['infent_price'];
									}else{
										$childprice += (float)$row1['child_price'];
									}
								}
							}
							$checkfordeals = "SELECT pd.dealamount,  pd.dealtype,pd.booksdate,pd.bookedate FROM prop_deals pd
								LEFT JOIN prop_deals_appliedrooms pdar ON pd.dealId = pdar.dealId
								WHERE ('$ddate' BETWEEN pd.dealsdate AND pd.dealedate) AND  pd.propId='$propId' AND pdar.prop_acId='$prop_acId' ";
							$rs2=$this->dbconnect()->query($checkfordeals);
							$rs2->data_seek(0);
							$dtotal = $row1['price'] + $childprice;
							$newdaystotal = 0;
							if($row2 = $rs2->fetch_assoc()){
								$bstatus = 0;
								if($row2['booksdate'] != null){
									$now = time();
									if(strtotime($row2['booksdate']) <= $now && strtotime($row2['bookedate']) >= $now) {
									    $bstatus = 1;
									}
								}else{	$bstatus = 1;	}
								if($bstatus == 1){
									if($row2['dealtype'] == 1) { $newdaystotal = $dtotal-($dtotal*($row2['dealamount']/100)); }
									else {  $newdaystotal = $dtotal-$row2['dealamount'];  }
									$row1['newdaytotal'] = $newdaystotal;
								}
								//var_dump($row2);
							}
							if($newdaystotal == 0) $ndaystotal += $dtotal; else $ndaystotal += $newdaystotal;
							$daystotal += $dtotal;
							$row1['daytotal'] = $dtotal;
							$tt[] = $row1;
							
						}else{
							$error = 1;
							break;
							//
							
						}
					}
					if($error !== 1) {

						$result['info'] = $row;
						$result['info']['ntotal'] = $ndaystotal;
						$result['info']['total'] = $daystotal;
						$result['rates'] = $tt;
						$tt ='';
						$fullresults[] = $result;
					}
				}
				$test[$key]= $fullresults;
				$fullresults = '';
			}
			return $test;
		}
	}
	function getbookingoptionsshort($query,$propId=NULL){
		$error = 0 ;
		$fullresults = '';
		if($propId === NULL){
			$shortname_type = explode("||", $query['shortname_type']) ;
			$propId =  $this->getpropid($shortname_type[0]);
		}
		if(!isset($propId)) $error = 1;
		if($error != 1){
			$result = array(
					'info'=>array()
					//'rates'=>array()
			);
			$no_rooms =$query['rooms'];
			$checkindate = $query['checkindate'];
			$checkoutdate = $query['checkoutdate'];
			$no_adults = array_map(function($details) {
				  return $details;
				}, $query['adult']);
			$min_adult = max($no_adults);
			$no_child = array_map(function($details) {
				  return $details;
				}, $query['child']);
			$min_child = max($no_adults);
			$no_rooms =$query['rooms'];
			$getallroomtypes = "SELECT pac.prop_acId,pac.no_rooms FROM prop_accomadations pac
				WHERE  pac.no_rooms >= 1 AND pac.propId = '$propId' ";
			$rs1=$this->dbconnect()->query($getallroomtypes);
			$rs1->data_seek(0);
			while($row1 = $rs1->fetch_assoc()){
				$amount_available[] = $row1;
			};
			// needto check if totall available rooms is greater or equal to whats needed ** important..
			foreach($query['adult'] as $key => $adult){
				if($query['child'][$key] == "0"){
					$num_child = 0;
				}else{
					$num_child = count($query['child'][$key]);
				} 
				$today = date("Y-m-d");
				$totalp = $adult + $num_child;
				$checkifrooms = "SELECT pac.prop_acId,pac.ac_name,pac.no_rooms,(pac.max_adult + pac.max_child) as totalp, pac.max_adult, pac.max_child FROM prop_accomadations pac
				INNER JOIN prop_accomadation_rates pacr
				ON pac.prop_acId = pacr.prop_acId
				WHERE pac.max_adult >= '$adult'  AND pac.no_rooms >= 1  AND pac.propId = '$propId'
				AND ('$today' <= '$checkindate' AND pac.availableto >= '$checkoutdate')
				GROUP BY pac.prop_acId
				HAVING  totalp >= '$totalp'";
				$rs=$this->dbconnect()->query($checkifrooms);
				$rs->data_seek(0);
				while($row = $rs->fetch_assoc()){
					$error = 0;
					$prop_acId = $row['prop_acId'];
					$nofinfent=0; $nofchild=0;
					if($num_child != 0){
						foreach($query['child'][$key] as $age){
							//var_dump($age);
							if($age < 2){
								$nofinfent += 1;
							}else{
								$nofchild += 1;
							}
						}
					}
					$row['nofchild'] = $nofchild; $row['nofinfent'] = $nofinfent;$row['nofadult'] = $adult;

					//echo $row['no_rooms'].'<br />';
					$begin = new DateTime($checkindate);
					$end = new DateTime($checkoutdate);

					$interval = DateInterval::createFromDateString('1 day');
					$period = new DatePeriod($begin, $interval, $end);
					
					$daystotal = 0; $ndaystotal = 0;
					foreach ( $period as $dt ){
						$ddate = $dt->format("Y-m-d");
						$getpricesforeachdate = "SELECT pacr.rateId,pacr.child_price,pacrd.price,pacr.infent_price,'$ddate' as ddate, pacrd.no_adult FROM prop_accomadation_rates pacr
						LEFT JOIN prop_ac_ratedetails pacrd
						ON pacr.rateId = pacrd.rateId
						WHERE pacr.prop_acId = '$prop_acId' AND pacrd.no_adult = '$adult' AND ('$ddate' BETWEEN pacr.date_validfrom AND pacr.date_validto)";
						$rs1=$this->dbconnect()->query($getpricesforeachdate);
						$rs1->data_seek(0);
						if($row1 = $rs1->fetch_assoc()){
							$childprice = 0;
							if($num_child != 0){
								foreach($query['child'][$key] as $age){
									//var_dump($age);
									if($age < 2){
										$childprice += (float)$row1['infent_price'];
									}else{
										$childprice += (float)$row1['child_price'];
									}
								}
							}
							$checkfordeals = "SELECT pd.dealamount,  pd.dealtype,pd.booksdate,pd.bookedate FROM prop_deals pd
								LEFT JOIN prop_deals_appliedrooms pdar ON pd.dealId = pdar.dealId
								WHERE ('$ddate' BETWEEN pd.dealsdate AND pd.dealedate) AND  pd.propId='$propId' AND pdar.prop_acId='$prop_acId'";
							$rs2=$this->dbconnect()->query($checkfordeals);
							$rs2->data_seek(0);
							$dtotal = $row1['price'] + $childprice;
							$newdaystotal = 0;
							if($row2 = $rs2->fetch_assoc()){
								$bstatus = 0;
								if($row2['booksdate'] != null){
									$now = time();
									if(strtotime($row2['booksdate']) <= $now && strtotime($row2['bookedate']) >= $now) {
									    $bstatus = 1;
									}
								}else{	$bstatus = 1;	}
								if($bstatus == 1){
									if($row2['dealtype'] == 1) { $newdaystotal = $dtotal-($dtotal*($row2['dealamount']/100)); }
									else {  $newdaystotal = $dtotal-$row2['dealamount'];  }
									$row1['newdaytotal'] = $newdaystotal;
								}
								//var_dump($row2);
							}
							if($newdaystotal == 0) $ndaystotal += $dtotal; else $ndaystotal += $newdaystotal;
							$daystotal += $dtotal;
							//$row1['daytotal'] = $dtotal;
							$tt[] = $dtotal;
							
						}else{
							$error = 1;
							break;
							//
							
						}
					}
					if($error !== 1) {
						$result['info']['prop_acId'] = $row['prop_acId'];
						$result['info']['no_rooms'] = $row['no_rooms'];
						$result['info']['ntotal'] = $ndaystotal;
						$result['info']['total'] = $daystotal;
						//$result['rates'] = $tt;
						$tt ='';
						$fullresults[] = $result;
					}
				}
				$test[$key]= $fullresults;
				$fullresults = '';
			}
			return $test;
		}
	}
	function bookinginregion($loc,$searched){
//		echo round(microtime(true) * 1000);
		//$id=$searched['locId'];
		$type = $searched['type'];
		if($type == 2){
			$feild = 'countryId';
		}else if($type == 3){
			$feild = 'regionId';
		}else{
			$feild = 'cityId';
		}
		$no_rooms = $searched['rooms'];
		$locId = $this->getlocdetails($loc)['id'];
		$checkindate =$searched['checkindate'];
		$checkoutdate = $searched['checkoutdate'];
		$begin = new DateTime($checkindate);
		$end = new DateTime($checkoutdate);
		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($begin, $interval, $end);
		$today = date("Y-m-d");
		$sql = "SELECT propId,shortname,fullname,starrating FROM property WHERE {$feild} = '$locId'";
		$rs=$this->dbconnect()->query($sql);
		$propprice = '';
		while($row = $rs->fetch_assoc()){
			$error = 0;
			$totalrooms = '';
			$propId = $row['propId'];
			$checkforrooms = "SELECT sum(no_rooms) as nrooms FROM prop_accomadations WHERE 
				('$today' <= '$checkindate' AND availableto >= '$checkoutdate') AND propId ='$propId' HAVING nrooms >= '$no_rooms'";
				$rs1=$this->dbconnect()->query($checkforrooms);
				if($row1 = $rs1->fetch_assoc()){
					
						$test = $this->getbookingoptionsshort($searched,$propId);
						foreach ($test as $rn => $rm) {
							if(count($rm) != 1){
								foreach ($rm as $t=> $roomtype) {
									$dublicated = 0;
									if(!empty($totalrooms)){
										foreach ($totalrooms as $key1 => $value1) {
											if($value1['prop_acId'] === $roomtype['info']['prop_acId']){
												$dublicated = 1;
											}
										}
									}
									if($dublicated != 1){
										$totalrooms[$roomtype['info']['prop_acId']]= array('prop_acId'=>$roomtype['info']['prop_acId'], 'no_rooms'=>$roomtype['info']['no_rooms'],'no_selected'=>0) ;	
									}
								}
							}	
						}
						$total = 0; $ntotal =0;
						foreach ($test as $rn => $rm) {
							if(count($rm) != 1){
								usort($rm, function($a, $b) {
						    		return $a['info']['total'] - $b['info']['total'];
								});	
								foreach ($rm as $t=> $roomtype) {
									if($roomtype['info']['no_rooms'] != $totalrooms[$roomtype['info']['prop_acId']]['no_selected']){
										$totalrooms[$roomtype['info']['prop_acId']]['no_selected']++;
										$total += $roomtype['info']['total'];
										$ntotal += $roomtype['info']['ntotal'];
										break;
									}
								}
							}else{
								$total = $rm[0]['info']['total'];
								$ntotal = $rm[0]['info']['ntotal'];
							}
						}
						$row['total'] = $total; $row['ntotal'] = $ntotal;
						$propprice[] = $row;	
				}else{
					$error = 1;
				}
		}
		return $propprice;
		//var_dump($propprice);
	}
	function propgallery($propId,$type){
		$result = '';
		$con = $this->dbconnect();
		$sql="SELECT * FROM prop_gallery WHERE propId='$propId' AND type='$type'";
		$rs=$con->query($sql);
	  	$rs->data_seek(0);
		while($row = $rs->fetch_assoc()){
			$result[]=$row;
		}
		return	$result;
	}
	function addtogallery($filename,$propId,$type){
		$sql="INSERT INTO prop_gallery (propId,img_name,type) VALUES('$propId','$filename',$type)";
		$this->dbconnect()->query($sql);

		//var_dump('expression');
	}
	function delgimg($id){
		$sql="DELETE FROM prop_gallery WHERE img_name = '$id'";
		$this->dbconnect()->query($sql);		
	}
	function deletehotels($id){
		$sql="DELETE FROM property WHERE propId = '$id'";
		$this->dbconnect()->query($sql);			
	}
	function autocompletesearch($keywords,$limit){
		if($keywords != ''){
			$pieces = explode(" ", $keywords);
			$count = count($pieces);
			$search;
			if($count > 1){
				for($it=0; $it<$count; $it++){
					if($it==0){
						$search = "lookupdata LIKE '%".$pieces[$it]."%'";
					}else{
						$search .= " AND Lookupdata LIKE '%".$pieces[$it]."%'";
					}
				}
			}else{
				$search = "lookupdata LIKE '%".$keywords."%'";
			}
		}
		//echo $search;
		$sql = "SELECT * FROM lookuptable WHERE ({$search}) AND (ltype='2' OR ltype='3' OR ltype='4') LIMIT $limit;";
		$sql .= "SELECT * FROM lookuptable WHERE ({$search}) AND ltype='6' LIMIT $limit;";
		$sql .= "SELECT * FROM lookuptable WHERE ({$search}) AND ltype='5' LIMIT $limit;";
		$sql .= "SELECT * FROM lookuptable WHERE ({$search}) AND ltype='1' LIMIT $limit;";
		$mysqli = $this->dbconnect();
		$alldata = array();
		if($mysqli->multi_query($sql))
		 { 
		    do
		    {
		        if($result=$mysqli->store_result())
		        {
		            while($row=$result->fetch_assoc())
		            {
		            	$alldata[] = $row;
		            }
		            $result->free();
		        }
		    }while($mysqli->more_results() && $mysqli->next_result());
		 }
		return $alldata;

	}
	function autocomplete($term,$type){
		if($type == 'country'){
			$aColumns = array('country','countryId','fullname');
		}elseif($type == 'region'){
			$aColumns = array('region','regionId','fullname');
		}
		$pieces = explode(" ", $term);
		$count = count($pieces);
		$search;
		if($count > 1){
			for($i=0; $i<$count; $i++){
				if($i == 0){
					$search = "".$aColumns[1]." LIKE '%".$pieces[$i]."%'";
				}else{
					$search .= " AND ".$aColumns[1]." LIKE '%".$pieces[$i]."%'";
				}
			}
		}else{
				$search = "".$aColumns[1]." LIKE '%".$term."%'";
		}
		$qstring = "SELECT ".$aColumns[2].", ".$aColumns[1]." FROM ".$aColumns[0]." WHERE ((".$search.") or ".$aColumns[2]." LIKE '%".$term."%') LIMIT 10";
		$rs=$this->dbconnect()->query($qstring);
	  	$rs->data_seek(0);
	  	$row_set = '';
		while($row = $rs->fetch_assoc()){
				$r['value']=htmlentities(stripslashes($row[$aColumns[2]]));
				$r['id']=(int)$row[$aColumns[1]];
				$row_set[] = $r;
		}
		return $row_set;
		//$sql=
	}
	function qlookup($keywords){
		if($keywords != ''){
			$pieces = explode(" ", $keywords);
			$count = count($pieces);
			$search;
			if($count > 1){
				for($it=0; $it<$count; $it++){
					if($it==0){
						$search = "lookupdata LIKE '%".$pieces[$it]."%'";
					}else{
						$search .= " AND Lookupdata LIKE '%".$pieces[$it]."%'";
					}
				}
			}else{
				$search = "lookupdata LIKE '%".$keywords."%'";
			}
			$result = '';
			$sql = "SELECT * FROM lookuptable WHERE ({$search}) AND (ltype='1');";
			$rs=$this->dbconnect()->query($sql);
		  	$rs->data_seek(0);
			while($row = $rs->fetch_assoc()){
				$result[] = $row;
			}
			return $result;

		}
	}

	function getfeatured(){
		$query = "SELECT p.fullname, p.shortname ,r.fullname as region,ct.fullname as city, c.fullname as country
					FROM property p
					INNER JOIN country c
					ON p.countryId = c.countryId
					LEFT JOIN city ct
					ON p.cityId = ct.cityId
					LEFT JOIN region r
					ON p.regionId = r.regionId
					WHERE p.featured = 1 ";
		$rs=$this->dbconnect()->query($query);
	  	$rs->data_seek(0);
		while($row = $rs->fetch_assoc()){
			var_dump($row);
		}

	}

	function updatelookuptable(){
		$query = 		"SELECT CONCAT_WS(', ', p.fullname, ct.fullname ,r.fullname ,c.fullname) as lookupdata, p.propId as originalId, p.shortname as shortname, '1' as ltype
						FROM property p
						INNER JOIN country c
						ON p.countryId = c.countryId
						LEFT JOIN region r
						ON p.regionId = r.regionId
						LEFT JOIN city ct
						ON p.cityId = ct.cityId;";
		$query .=		"SELECT fullname as lookupdata,shortname as shortname, countryId as originalId, '2' as ltype FROM country;";
		$query .=		"SELECT CONCAT_WS(', ',r.fullname,c.fullname) as lookupdata, r.regionId as originalId,r.shortname as shortname, '3' as ltype
						FROM region r
						INNER JOIN country c
						ON r.countryId= c.countryId;";
		$query .=		"SELECT CONCAT_WS(', ',ct.fullname,r.fullname, c.fullname) as lookupdata, ct.cityId as originalId, ct.shortname as shortname, '4' as ltype
						FROM city ct
						INNER JOIN country c
						ON ct.countryId = c.countryId
						LEFT JOIN region r
						ON ct.regionId= r.regionId;";
		$query	.=		"SELECT CONCAT_WS(', ',lmt.fullname,c.fullname) as lookupdata, lmt.lmtId as originalId, lmt.shortname as shortname, '5' as ltype
						FROM lmtrans lmt
						INNER JOIN country c
						ON lmt.countryId = c.countryId
						WHERE type = 1;";
		$query	.=		"SELECT CONCAT_WS(', ',lmt.fullname,c.fullname) as lookupdata, lmt.lmtId as originalId, lmt.shortname as shortname, '6' as ltype
						FROM lmtrans lmt
						INNER JOIN country c
						ON lmt.countryId = c.countryId
						WHERE type = 2;";			

						$mysqli = $this->dbconnect();
			if($mysqli->multi_query($query))
			 { 
			    do
			    {
			        if($result=$mysqli->store_result())
			        {
			            while($row=$result->fetch_assoc())
			            {
			            	$alldata[] = $row;
			            }
			            $result->free();
			        }
			    }while($mysqli->more_results() && $mysqli->next_result());
			 }
			 foreach($alldata as $data){
			 	// this should be divided in to two parts to reduce system load (reminder for later)
			 	$extra  = '';
				$lookupdata = $data['lookupdata']; $shortname = $data['shortname']; $ltype = $data['ltype']; $originalId = $data['originalId'];

			 	if($ltype == 1){
			 		$cheapest = "SELECT pacrd.price
									FROM property p
									LEFT JOIN prop_accomadations pac
									ON p.propId = pac.propId
									LEFT JOIN prop_accomadation_rates pacr
									ON pac.prop_acId = pacr.prop_acId
									LEFT JOIN prop_ac_ratedetails pacrd
									ON pacr.rateId = pacrd.rateId AND no_adult = 1
									WHERE p.shortname = '$shortname' AND (CURDATE() BETWEEN pacr.date_validfrom AND pacr.date_validto)ORDER BY pacrd.price";
					$rs1=$this->dbconnect()->query($cheapest);
				  	$rs1->data_seek(0);
					if($cprow = $rs1->fetch_assoc()){
						$extra = $cprow['price'];
					}	
			 	}else if($ltype == 2 || $ltype == 3 || $ltype == 3){
			 		if($ltype == 2){
			 			$feild = 'countryId';
			 		}else if($ltype == 3){
			 			$feild = 'regionId';
			 		}else{
			 			$feild = 'city';
			 		}
			 		$hotelin = "SELECT * FROM property WHERE {$feild} = '$originalId'";
			 		$rs=$this->dbconnect()->query($hotelin);
			 		$extra = $rs->num_rows;
			 	}else if($ltype == 5 || $ltype == 6){
			 		$nearby = "SELECT * FROM prop_nearby WHERE lmtId = '$originalId'";
			 		$rs=$this->dbconnect()->query($nearby);
			 		$extra = $rs->num_rows;			 		
			 	}else{
			 		$extra = '';
			 	}

			 	var_dump($extra);


				$lookupdata="'" . $mysqli->real_escape_string($lookupdata) . "'";
				$sql="INSERT INTO lookuptable (lookupdata,shortname,ltype,originalId,extra) VALUES ($lookupdata,'$shortname','$ltype','$originalId','$extra')";
				if($mysqli->query($sql) === false) {
				  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
				}
			}

			$mysqli->close();
	}
	function adddeals($post){
		$propId = $post['propId'];
		$dealsdate = $post['dealsdate'];
		$dealedate = $post['dealedate'];
		$booksdate = $post['booksdate'];
		$bookedate = $post['bookedate'];
		$dealamount = $post['dealamount'];
		$dealtype = $post['dealtype'];

		if($post['booksdate'] == "" && $post['bookedate'] == "") {
			$sql="INSERT INTO prop_deals (propId,dealsdate,dealedate,dealamount,dealtype,status)
				VALUES ('$propId','$dealsdate','$dealedate','$dealamount','$dealtype',1)";		
		}else{
			$sql="INSERT INTO prop_deals (propId,dealsdate,dealedate,booksdate,bookedate,dealamount,dealtype,status)
				VALUES ('$propId','$dealsdate','$dealedate','$booksdate','$bookedate','$dealamount','$dealtype',1)";		}
		if($this->dbconnect()->query($sql) === false) {
			return 'Error';
		}else{
			return 'success';
		}
	}
	function updatedeals($post,$dealId){
		$propId = $post['propId'];
		$dealsdate = $post['dealsdate'];
		$dealedate = $post['dealedate'];
		$booksdate = $post['booksdate'];
		$bookedate = $post['bookedate'];
		$dealamount = $post['dealamount'];
		$dealtype = $post['dealtype'];
		$status = $post['status'];
		if($post['booksdate'] == "" && $post['bookedate'] == "") {
			$sql="UPDATE prop_deals SET propId='$propId',dealsdate='$dealsdate',dealedate='$dealedate',dealamount='$dealamount',dealtype='$dealtype',status='$status' WHERE dealId = '$dealId'";
		}else{
			$sql="UPDATE prop_deals SET propId='$propId',dealsdate='$dealsdate',dealedate='$dealedate',booksdate='$booksdate',bookedate='$bookedate',dealamount='$dealamount',dealtype='$dealtype',status='$status' WHERE dealId = '$dealId'";
		}
		if($this->dbconnect()->query($sql) === false) {
			return 'Error';
		}else{
			$delcrooms = "DELETE FROM prop_deals_appliedrooms WHERE dealId = '$dealId'";
			$this->dbconnect()->query($delcrooms);
			if(isset($post['room'])){
				foreach ($post['room'] as $key => $rm) {
					$sql1 = "INSERT INTO prop_deals_appliedrooms (dealId,prop_acId) VALUES ('$dealId','$rm')";
					$this->dbconnect()->query($sql1);
				}
			}
			return 'success';
		}
	}
	function deldeals($id){
		$sql="DELETE FROM prop_deals WHERE dealId = '$id'";
		$sql1="DELETE FROM prop_deals_appliedrooms WHERE dealId = '$id'";
		$this->dbconnect()->query($sql);
		$this->dbconnect()->query($sql1);
	}
	function getdeals($term,$filter='p.fullname',$forder='ASC',$start=0){
		// if($filter != '') $filter = 'p.fullname'; else $filter 'p'.$filter;
		// if($forder != '') $forder = 'DESC'; 
		$result = '';
		$deals = "SELECT d.*, p.propId,pt.proptype,p.ptype,p.totalrooms,p.shortname,p.fullname,p.starrating,p.regionId,p.address,c.fullname as country,c.shortname as countryshort,r.fullname as region,r.shortname as regionshort,ct.fullname as city,ct.shortname as cityshort 
			FROM prop_deals d
			LEFT JOIN property p ON
			d.propId = p.propId
			LEFT JOIN country c ON
			p.countryId = c.countryId
			LEFT JOIN region r ON
			p.regionId = r.regionId
			LEFT JOIN city ct ON
			p.cityId = ct.cityId
			LEFT JOIN prop_type pt ON
			p.ptId = pt.ptId
			WHERE d.status = 0
			ORDER BY {$filter} {$forder}
			LIMIT $start, 6";
		// $hotels = "SELECT p.propId,pt.proptype,p.ptype,p.totalrooms,p.shortname,p.fullname,p.starrating,p.regionId,p.address,c.fullname as country,c.shortname as countryshort,r.fullname as region,r.shortname as regionshort,ct.fullname as city,ct.shortname as cityshort FROM property p
		// 	LEFT JOIN country c ON
		// 	p.countryId = c.countryId
		// 	LEFT JOIN region r ON
		// 	p.regionId = r.regionId
		// 	LEFT JOIN city ct ON
		// 	p.cityId = ct.cityId
		// 	LEFT JOIN prop_type pt ON
		// 	p.ptId = pt.ptId
		// 	 WHERE {$feild} = '$id'
		// 	 ORDER BY {$filter} {$forder}
		// 	 LIMIT $start, 6";
		// $gettotal = "SELECT * FROM property p  WHERE {$feild} = '$id'";
		$rs=$this->dbconnect()->query($deals);
		// $rs1=$this->dbconnect()->query($gettotal);
		// $TotalRcount = $rs1->num_rows;
		$rs->data_seek(0);
		while($row = $rs->fetch_assoc()){
			$dealId = $row['dealId'];
			$dealsdate = $row['dealsdate'];
			$getmoredetails = "SELECT par.prop_acId,pa.ac_name,pard.price FROM prop_deals_appliedrooms pdar
				LEFT JOIN prop_accomadations pa ON pdar.prop_acId = pa.prop_acId
				LEFT JOIN prop_accomadation_rates par ON pdar.prop_acId=par.prop_acId
				LEFT JOIN prop_ac_ratedetails pard ON par.rateId = pard.rateId
				WHERE pdar.dealId = '$dealId' AND ('$dealsdate' BETWEEN par.date_validfrom AND par.date_validto) AND pard.no_adult=1
				ORDER BY pard.price";
			$rs2=$this->dbconnect()->query($getmoredetails);
			$drd = '';
			while($row2 = $rs2->fetch_assoc()){
				$drd[]=$row2;
			}
			$row['nprice'] = $drd[0]['price'];
			if($row['dealtype'] == 1) { $row['dprice'] =  number_format($drd[0]['price'] - ($drd[0]['price']/$row['dealamount']),1); }
			else{ $row['dprice'] =  $drd[0]['price'] - $row['dealamount'];  }
			$row['rdetails'] = $drd;
			//var_dump($drd[0]['price']);
			$result['deals'][] = $row;
		}
		// $result['total'] = $TotalRcount;
		return $result;

	}
	function pickrandomdeals(){
		$result = '';
		$deals = "SELECT d.*, p.propId,pt.proptype,p.ptype,p.totalrooms,p.shortname,p.fullname,p.starrating,p.regionId,p.address,c.fullname as country,c.shortname as countryshort,r.fullname as region,r.shortname as regionshort,ct.fullname as city,ct.shortname as cityshort 
			FROM prop_deals d
			LEFT JOIN property p ON
			d.propId = p.propId
			LEFT JOIN country c ON
			p.countryId = c.countryId
			LEFT JOIN region r ON
			p.regionId = r.regionId
			LEFT JOIN city ct ON
			p.cityId = ct.cityId
			LEFT JOIN prop_type pt ON
			p.ptId = pt.ptId
			WHERE d.status = 0
			ORDER BY RAND()
			LIMIT 3";
		$rs=$this->dbconnect()->query($deals);
		$rs->data_seek(0);
		while($row = $rs->fetch_assoc()){
			$dealId = $row['dealId'];
			$dealsdate = $row['dealsdate'];
			$getmoredetails = "SELECT par.prop_acId,pa.ac_name,pard.price FROM prop_deals_appliedrooms pdar
				LEFT JOIN prop_accomadations pa ON pdar.prop_acId = pa.prop_acId
				LEFT JOIN prop_accomadation_rates par ON pdar.prop_acId=par.prop_acId
				LEFT JOIN prop_ac_ratedetails pard ON par.rateId = pard.rateId
				WHERE pdar.dealId = '$dealId' AND ('$dealsdate' BETWEEN par.date_validfrom AND par.date_validto) AND pard.no_adult=1
				ORDER BY pard.price";
			$rs2=$this->dbconnect()->query($getmoredetails);
			$drd = '';
			while($row2 = $rs2->fetch_assoc()){
				$drd[]=$row2;
			}
			$row['nprice'] = $drd[0]['price'];
			if($row['dealtype'] == 1) { $row['dprice'] =  number_format($drd[0]['price'] - ($drd[0]['price']/$row['dealamount']),1); }
			else{ $row['dprice'] =  $drd[0]['price'] - $row['dealamount'];  }
			$result['deals'][] = $row;
		}
		return $result;		
	}

	function getdeal($dealId){
		$deals = "SELECT * FROM prop_deals WHERE dealId = '$dealId'";
		$rs=$this->dbconnect()->query($deals);
		if($row = $rs->fetch_assoc()){
			return $row;	
		}
			
	}

	function deals($term,$limit,$start,$order,$ordert,$type){
		//echo 'tset';
		$this->dbconnect();
			$table = 'prop_deals d LEFT JOIN property p ON d.propId = p.propId';
			$aColumns = array('fullname');
			$aFeilds = "d.*, p.propId,p.fullname, IF( d.dealtype ='1', '%','USD') as type";
			//$aFeilds = "lmtId,fullname,shortname, IF( lmtrans.type ='1', 'Transport','Landmarks') as type";
		
		
		
		$sWhere = "";
		if($term != ""){
			$sWhere = "WHERE (p.fullname LIKE '%".$term."%') ";
		}else{
			$sWhere .= '';
		}

		if($type == 'count'){
			$query = "SELECT {$aFeilds} FROM {$table} {$sWhere}";
			$rs1=$this->dbconnect()->query($query);
			$results = $rs1->num_rows;	
			//var_dump(expression)	
		//echo $query;
		}else{
			$query = "SELECT {$aFeilds} FROM {$table} {$sWhere} order by {$order} {$ordert} limit $start,$limit";
		//	echo $query;
				$rs=$this->dbconnect()->query($query);
				while($row = $rs->fetch_assoc()){
					$results[]=$row;
				}	
		}
		if(isset($results)) return $results; else return 'NoData';
	}

	function getdatafromold(){

	}
}
?>