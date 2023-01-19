<?php 
	ini_set('display_errors', '0');
	// 오류 표시 안함
	/*
		http://m.bus.go.kr/mBus/subway/getStatnByRoute.bms
		http://m.bus.go.kr/mBus/subway/getArvlByInfo.bms

		POST

		subwayId
		statnId
	*/

	function post($url, $fields)
	{
		$post_field_string = http_build_query($fields, '', '&');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field_string);
		curl_setopt($ch, CURLOPT_POST, true);
		$response = curl_exec($ch);
		$result = iconv("EUC-KR", "UTF-8", $response);
		curl_close ($ch);
		return $result;
	}
	function contains($a, $b) {
		$pos = strpos($a, $b);
		if($pos !== false) return true;
		else return false;
	}
	if(isset($_POST['id']) == true) {
		$msg = (string)$_POST['id'];
		$id = array(
			'0'=>'1001','1'=>'1002','2'=>'1003','3'=>'1004','4'=>'1005','5'=>'1006','6'=>'1007','7'=>'1008',
			'8'=>'1009','9'=>'1067','10'=>'1063','11'=>'1065','12'=>'1075','13'=>'1077','14'=>'1071','15'=>'1092'
		);
		$id = $id[$msg];
		$res = array();
		
		$result = post('http://m.bus.go.kr/mBus/subway/getStatnByRoute.bms', array('subwayId'=>$id));
		$result = json_decode($result);
		$result = $result->resultList;

		for($i = 0; $i < count($result); $i++) {
			$t = $result[$i];
			$down = ''; $up = '';

			if($t->existYn1 == "T") $down = "f";
			else if($t->existYn1 == "Y") $down = "e";
			else $down = "n";

			if($t->existYn2 == "T") $up = "f";
			else if($t->existYn2 == "Y") $up = "e";
			else $up = "n";

			$id_ = substr($t->statnId, 4);
			$id_ = (int)$id_ + 0;
			$id_ = trim(str_replace(' 80', 'P', ' '.(string)$id_));
			$id_ = trim(str_replace(' 75', 'K', ' '.(string)$id_));
			$id_ = trim(str_replace(' 47', 'L', ' '.(string)$id_));
			if(strlen(preg_replace("/[^0-9]*/s", "", $id_)) > 3) $id_ = substr($id_, 0, 3).'-'.substr($id_, 3-strlen($id_) ,strlen($id_)-3);

			$res[$i] = array(
				'name'=>($t->statnNm), // 역 이름
				'exist_down'=>$down, // 아래방향 도착여부
				'exist_up'=>$up, // 위방향 도착여부
				'id'=>$id_, // 정류장 번호
			);
		}

		if(implode('', $res) != '') echo json_encode(array('result'=>$res));
		else echo $json = '{"result":[]}';
	}
?>