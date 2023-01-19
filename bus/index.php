<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="Bus Information">
    <meta name="description" content="Real-time information">

    <meta property="og:title" content="Bus Information" />
    <meta property="og:description" content="Real-time information" />
    <meta property="og:locale" content="ko_KR" />
    <title>Bus Information</title>

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
		table.chart th:nth-child(2n-1), table.chart td:nth-child(2n-1) {
            margin: 0;
            padding: 0;
            border-collapse: collapse;
			width: 100%;
            text-align: center;
            border-bottom: 1px solid #cccccc;
        }
		table.chart th:nth-child(2n), table.chart td:nth-child(2n) {
			margin: 0;
            padding: 0;
            border-collapse: collapse;
			width: 100%;
			text-align: center;
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
			height: 2.2em;
			width: 12em;
			box-sizing: border-box;
			border-radius: 7px;
			border: none;
			padding: 1em 0 1em 3em;
			font-size: 1em;
			font-family: "Montserrat", "Noto Sans KR", sans-serif;
			background-color: #E7E7E7;
			background-image: url('./search.svg');
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
							<h2 style="float:left">Bus Info<small>(demo)</small></h2>
						</td>
						<td>
							<span style="float:right">
								<input type="text" id="id" name="id" placeholder="버스번호 입력" style="text-align:center;" autocomplete="off">
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
					'http://ssungjoe.dothome.co.kr/bus/api.php',
					array('id'=>urlencode($id))
				).';';
				//이 밑은 이상한 점 붙어나오는거 떼는거임
				$json = explode('{', $json);
				array_splice($json, 0, 1);
				$json = '{'.implode('{', $json);
			}
			else {
				$json = '{"result":[]};';
			}
			echo 'let json = '.$json;
		?>
		
		json = json['result']; // 버스 정보
		let container = document.getElementById('container');
		for(let z = 0; z < json.length; z++) {
			if(z > 0) {
				let divider = document.createElement('div');
				divider.className = 'ui clearing divider';
				container.appendChild(divider);
			}
			let info = document.createElement('div');
			let temp = json[z],
			start_p = temp.start_point, //기점
			end_p = temp.end_point, //종점
			start_t = temp.start_time, //첫차
			end_t = temp.end_time, //막차
			bus_int = temp.bus_interval, //배차간격
			bus_type = temp.bus_type, // 버스 종류(색)
			bus_num = temp.bus_num, // 버스 번호
			bus_typeName = temp.bus_typeName, // 버스 유형
			buses = temp.buses + '대 운행 중'; //운행 중인 버스

			if(bus_int.indexOf('#') == -1) bus_int = '배차간격: ' + bus_int + '분';
			else bus_int = '배차간격: 1일 ' + bus_int.substr(1) + '회';
			bus_num = bus_num.replace(/(\(([^\)]+)\))/g, '');
			info.innerText = bus_num + '번 버스 ('+bus_typeName+')\n' + start_p +' ~ '+ end_p +'\n운행시간: '+ start_t +' ~ '+ end_t +'\n'+ bus_int +'\n'+ buses;
			container.appendChild(info);

			let row = document.createElement("table");
			row.className = 'chart';
			let name = document.createElement("th");
			let location = document.createElement("th");
			name.innerText = "정류장";
			location.innerText = "";
			row.appendChild(name);
			row.appendChild(location);
			for (let y = 0; y < temp.location.length; y++) {
				let cell = document.createElement("tr");
				let name_ = document.createElement("td");
				let location_ = document.createElement("td");
				let t = temp.location[y];

				name_.innerHTML = t.name + ((t.id == null) ? '' : ('<br><small>' + t.id + '</small>'));
				let color = 'glbyrvson'[bus_type];
				if(t.exist) {
					let img = document.createElement('img');
					img.setAttribute('src', 'http://ssungjoe.dothome.co.kr/bus/asset/exist_' + color + '.png');
					img.style.width = "50px";
					img.style.height = "50px";
					location_.appendChild(img);
				}
				else if(!t.exist){
					let img = document.createElement('img');
					img.setAttribute('src', 'http://ssungjoe.dothome.co.kr/bus/asset/none_' + color + '.png');
					img.style.width = "50px";
					img.style.height = "50px";
					location_.appendChild(img);
				}
				//location_.innerText = t.exist ? 'Y' : 'N';
				cell.appendChild(name_);
				cell.appendChild(location_);
				row.appendChild(cell);	
			}
			container.appendChild(row);
			container.appendChild(document.createElement("br"));
		}
		if(json.length == 0) {
			let intro = document.createElement('div');
			intro.innerText = '검색한 순간의 정보는 실시간이 맞지만,\n그 후로는 실시간으로 변동되는 것이 아니므로\n필요할 때마다 수동으로 새로고침해야 합니다.\n또한, 버스번호 앞에 지역명을 붙이면 상세한 결과를 검색할 수 있습니다.\n예시 : 인천 574\n오른쪽 상단에 검색어를 입력해주세요.';
			container.appendChild(intro);
		}
		//alert("완료");
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