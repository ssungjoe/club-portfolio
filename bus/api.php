<?php 
	ini_set('display_errors', '0');
	// 오류 표시 안함
	function get($url, $params=array()) 
	{ 
		$url = $url.'?'.http_build_query($params, '', '&');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
	function contains($a, $b) {
		$pos = strpos($a, $b);
		if($pos !== false) return true;
		else return false;
	}
	if(isset($_POST['id']) == true) {
		$msg = (string)$_POST['id'];
		$id_res = get('https://search.naver.com/search.naver', array('query'=>(urldecode($msg).'번 버스')));
		$id_res = explode('k_bus_thumb">', $id_res);
		array_splice($id_res, 0, 1);
		$id_arr = array();
		for($i = 0; $i < count($id_res); $i++) {
			$temp = $id_res[$i];
			$temp = explode('" target=', $temp)[0];
			$temp = explode('busId=', $temp)[1];
			array_push($id_arr, $temp);
		}
		array_splice($id_arr, count($id_arr)-2, 2);
		// $id_arr에 버스 id를 담아왔음

		$res = array();
		for($j = 0; $j < count($id_arr); $j++) { // 여러 id들 모두 확인
			$result = get( // 첫차, 막차 등
				'https://s.search.naver.com/p/mlocation/map/api2/getBusDetailInfo.nhn', 
				array(
					'_callback'=>'window.__jindo2_callback._bus_detail_info_0', 
					'output'=>'json',
					'caller'=>'pc_search',
					'includeCompanyInfo'=>'true',
					'doSeparateSection'=>'true',
					'includeNonStopBusStops'=>'true',
					'busID'=>$id_arr[$j]
				)
			);
			$result = explode('0(', $result)[1];
			$result = explode('})', $result)[0].'}';
			$result = json_decode($result)->message->result;
			$temp = array();
			
			$start_p = $result->busStartPoint;
			$end_p = $result->busEndPoint;
			$start_t = $result->busFirstTime;
			$end_t = $result->busLastTime;
			$interval = $result->busInterval;
			$type = $result->type;
			$typeName = $result->typeName;
			$num = $result->busNo;
			
			$start_p = str_replace('.', '/', $start_p);
			$end_p = str_replace('.', '/', $end_p);

			if(in_array($type, array('12'))) $type = 0; //green
			else if(in_array($type, array('1', '3', '10'))) $type = 1; //lightgreen
			else if(in_array($type, array('2', '11'))) $type = 2; //blue
			else if(in_array($type, array('13', '16', '20'))) $type = 3; //yellow
			else if(in_array($type, array('4', '6', '14', '15', '22'))) $type = 4; //red
			else if(in_array($type, array('21', '26'))) $type = 5; //violet
			else if(in_array($type, array('5'))) $type = 6; //skyblue
			else if(in_array($type, array('27'))) $type = 7; //orange
			else $type = 8; // array('7', '8', '9', '17', '18', '19', '23', '24', '25')

			$result_ = get( // 버스 위치
				'https://s.search.naver.com/n/api.pubtrans.map/2.0/live/getBusLocation.jsonp',
				array(
					'_callback'=>'window.__jindo2_callback._bus_location_list_0',
					'caller'=>'pc_search',
					'routeId'=>$id_arr[$j]
				)
			);
			$result_ = explode('0(', $result_)[1];
			$result_ = explode(');', $result_)[0];
			$result_ = json_decode($result_)->message->result;
			$loc = count($result_->metroList) == 1 ? $result_->busLocationList : $result_->metroList;
			for($k = 0; $k < count($loc); $k++) {
				$loc[$k] = $loc[$k]->stationSeq;
			}
			$buses = count($loc); // 운행 중인 버스 수
			$temp_ = array();
			$sta = $result->station;

			for($m = 0; $m < count($sta); $m++) {
				$sta[$m] = array(
					'name'=>(str_replace('.', '/', $sta[$m]->stationName)), // 정류장 이름
					'exist'=>(in_array($m, $loc) ? true : false), // 정류장 도착했는가
					'id'=>($sta[$m]->stationDisplayID) // 정류장 번호
				);
			}
			$temp = array(
				'start_point'=>$start_p, 'end_point'=>$end_p, 
				'start_time'=>$start_t, 'end_time'=>$end_t,
				'bus_interval'=>$interval, 'bus_type'=>(int)$type,
				'bus_num'=>$num, 'bus_typeName'=>$typeName,
				'buses'=>(string)$buses, 'location'=>$sta
			);
			array_push($res, $temp);
		}
		if(implode('', $res) != '') echo json_encode(array('result'=>$res));
		else echo $json = '{"result":[]}';
	}
?>