<?php
	function uuid(){
		return sprintf('%08x',mt_rand(0, 0xffffffff));
	}
	$img = $_POST['img'];
	$random = uuid();
	$uppath = "./img/".$random.".jpg";
	if(!is_dir("./img/")){
		mkdir("./img/");
	}
	$myfile = fopen($uppath, "wb") or die("ERROR");
	fwrite($myfile, base64_decode($img));
	fclose($myfile);
	echo $random;
 ?>