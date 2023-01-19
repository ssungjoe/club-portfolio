<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="Subway Information">
    <meta name="description" content="Real-time information">

    <meta property="og:title" content="Subway Information" />
    <meta property="og:description" content="Real-time information" />
    <meta property="og:locale" content="ko_KR" />
    <title>Subway Information</title>

    <script src="https://code.jquery.com/jquery-3.4.1.js"
            integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
            crossorigin="anonymous">
    </script>
    <link rel="stylesheet" type="text/css" href="http://ssungjoe.dothome.co.kr/semantic/semantic.css">
    <script src="http://ssungjoe.dothome.co.kr/semantic/semantic.js"></script>
	<style>
		table.chart {
			margin: 0;
            padding: 0;
            border-collapse: collapse;
		}
		table.chart th, table.chart td {
			margin: 0;
            padding: 0;
            border-collapse: collapse;
			width: 100%;
			text-align: center;
		}
		table.chart th:nth-child(3n-1), table.chart td:nth-child(3n-2) {
            border-bottom: 1px solid #cccccc;
        }
        table.chart caption {
            margin: 0;
            padding: 0;
            text-align: right;
			border-collapse: collapse;
        }
		table.chart thead th {
			background-color: #e9e9e9 !important;
            border-bottom: 1px solid #999999;
			border-collapse: collapse;
        }
		table.chart tfoot th {
            background-color: #e2e2e2 !important;
        }
		input {
			height: 2em;
			width: 3.7em;
			box-sizing: border-box;
			border-radius: 7px;
			border: none;
			font-size: 1em;
			font-family: "Montserrat", "Noto Sans KR", sans-serif;
			background-color: #E7E7E7;
			background-repeat: no-repeat;
			background-position: 1em;
			background-size: 7%;
			outline: none;
		}
		img {
			display: block;
		}
	</style>
</head>
<body>
    <div class="ui black inverted vertical footer segment">
        <div class="ui inverted secondary menu">
            <a id="menu" class="item" href="http://ssungjoe.dothome.co.kr">
                메인
            </a>
        </div>
    </div>
    <div class="ui grid">
        <div class="two wide column"></div>
        <div class="twelve wide column"></div>
        <div class="two wide column"></div>
    </div>
	<div class="ui grid">
        <div class="one wide column"></div>
        <div class="fourteen wide column">
			<form action="./index.php" method="post">
				<table width="100%">
					<tr>
						<td>
							<h2 style="float:left">Subway Info<small>(demo)</small></h2>
						</td>
						<td>
							<span style="float:right">
								<select id="id" name="id">
									<option value="" selected disabled hidden>
									<?php 
										if(isset($_POST['id'])) {
											echo array(
												'1호선', '2호선', '3호선', '4호선', '5호선', '6호선', '7호선',
												'8호선', '9호선', '경춘선', '경의중앙선', '공항', '분당선',
												'신분당','수인선', '우이신설'
											)[(string)$_POST['id']];
										} 
										else echo '1호선';
									?>
									</option>
									<option value="0">1호선</option>
									<option value="1">2호선</option>
									<option value="2">3호선</option>
									<option value="3">4호선</option>
									<option value="4">5호선</option>
									<option value="5">6호선</option>
									<option value="6">7호선</option>
									<option value="7">8호선</option>
									<option value="8">9호선</option>
									<option value="9">경춘선</option>
									<option value="10">경의중앙선</option>
									<option value="11">공항</option>
									<option value="12">분당선</option>
									<option value="13">신분당</option>
									<option value="14">수인선</option>
									<option value="15">우이신설</option>
								</select>
								<input type="submit">
							</span>
						</td>
					</tr>
				</table>
			</form>
			<br>
			<div id="container"></div>
		</div>
        <div class="one wide column"></div>
    </div>
    <script>
        <?php 
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
				curl_close ($ch);
				return $response;
			}
			if(isset($_POST['id'])) {
				$id = (string)$_POST['id'];
				$json = post(
					'http://ssungjoe.dothome.co.kr/subway/api.php',
					array('id'=>$id)
				).';';
				//이 밑은 이상한 점 붙어나오는거 떼는거임
				//$json = explode('{', $json);
				//array_splice($json, 0, 1);
				//$json = '{'.implode('{', $json);
			}
			else {
				$json = '{"result":[]};';
			}
			if($id != '') {
				$name = array(
					'0'=>'1호선', '1'=>'2호선', '2'=>'3호선', '3'=>'4호선', '4'=>'5호선', '5'=>'6호선',
					'6'=>'7호선', '7'=>'8호선', '8'=>'9호선', '9'=>'경춘선', '10'=>'경의중앙선',
					'11'=>'공항', '12'=>'분당선', '13'=>'신분당', '14'=>'수인선', '15'=>'우이신설'
				)[$id];
			}
			else {
				$id = '0';
				$name = '';
			}
			echo 'let json = '.$json.'let line = ["'.$name.'","'.$id.'"];';
		?>
		
		json = json['result']; // 지하철 정보

		document.querySelectorAll('option')[Number(line[1]) + 1].setAttribute('selected', 'selected');
		let container = document.getElementById('container');
		container.innerText = (line[0] != '') ? ('현재 노선: ' + line[0]) : '';

		var row = document.createElement("table");
		row.className = 'chart';
		/*
		var name = document.createElement("th");
		var location = document.createElement("th");
		var loc = document.createElement("th");
		name.innerText = "역";
		location.innerText = "역";
		loc.innerText = "역";
		row.appendChild(name);
		row.appendChild(location);
		row.appendChild(loc);
		*/

		for (let x = 0; x < json.length; x++) {
			let cell = document.createElement("tr");
			let name_ = document.createElement("td");
			let loc_1 = document.createElement("td");
			let loc_2 = document.createElement("td");
			let t = json[x];

			name_.innerHTML = t.name + '<br><small>' + t.id + '</small>';

			// 아래는 아래쪽 라인
			if(t.exist_down == 'f') {
				let img = document.createElement('img');
				img.setAttribute('src', 'http://ssungjoe.dothome.co.kr/subway/asset/fast_down_'+line[1]+'.png');
				img.style.width = "50px";
				img.style.height = "50px";
				loc_1.appendChild(img);
			}
			else if(t.exist_down == 'e'){
				let img = document.createElement('img');
				img.setAttribute('src', 'http://ssungjoe.dothome.co.kr/subway/asset/exist_down_'+line[1]+'.png');
				img.style.width = "50px";
				img.style.height = "50px";
				loc_1.appendChild(img);
			}
			else if(t.exist_down == 'n'){
				let img = document.createElement('img');
				img.setAttribute('src', 'http://ssungjoe.dothome.co.kr/subway/asset/none_'+line[1]+'.png');
				img.style.width = "50px";
				img.style.height = "50px";
				loc_1.appendChild(img);
			}
			// 아래는 위쪽 라인
			if(t.exist_up == 'f') {
				let img = document.createElement('img');
				img.setAttribute('src', 'http://ssungjoe.dothome.co.kr/subway/asset/fast_up_'+line[1]+'.png');
				img.style.width = "50px";
				img.style.height = "50px";
				loc_2.appendChild(img);
			}
			else if(t.exist_up == 'e'){
				let img = document.createElement('img');
				img.setAttribute('src', 'http://ssungjoe.dothome.co.kr/subway/asset/exist_up_'+line[1]+'.png');
				img.style.width = "50px";
				img.style.height = "50px";
				loc_2.appendChild(img);
			}
			else if(t.exist_up == 'n'){
				let img = document.createElement('img');
				img.setAttribute('src', 'http://ssungjoe.dothome.co.kr/subway/asset/none_'+line[1]+'.png');
				img.style.width = "50px";
				img.style.height = "50px";
				loc_2.appendChild(img);
			}
			cell.appendChild(name_);
			cell.appendChild(loc_1);
			cell.appendChild(loc_2);
			row.appendChild(cell);	
		}
		container.appendChild(row);
		container.appendChild(document.createElement("br"));
		if(json.length == 0) {
			let intro = document.createElement('div');
			intro.innerText = '검색한 순간의 정보는 실시간이 맞지만,\n그 후로는 실시간으로 변동되는 것이 아니므로\n필요할 때마다 수동으로 새로고침해야 합니다.';
			container.appendChild(intro);
		}
    </script>
    <br />
    <div class="ui black inverted vertical footer segment">
        <div class="ui grid">
            <div class="two wide column"></div>
            <div class="eight wide column">
                <div class="ui left aligned container">
                    Copyright &copy; 2020. Joe
                    <br />
                    All rights reserved.
                </div>
            </div>
            <div class="five wide column">
            </div>
            <div class="one wide column"></div>
        </div>
    </div>

    <script>
        $('#menu').click(function () {
            $('.ui.left.sidebar').sidebar('setting', 'transition', 'push').sidebar('toggle');
        });
    </script>
</body>
</html>