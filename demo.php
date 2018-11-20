<?php
$postdata = file_get_contents("php://input");
if (!empty($postdata)) {
	if ($postdata = @json_decode($postdata, true)) {
		foreach ($postdata as $k=>$v) {
			$_POST[$k] = $v;
		}
	}
}
$table=array();

$table['header'][] = array("name"=>"A");
$table['header'][] = array("name"=>"B");
$table['header'][] = array("name"=>"C");
$table['header'][] = array("name"=>"D");
$table['header'][] = array("name"=>"E");
$table['header'][] = array("name"=>"F");
$table['header'][] = array("name"=>"G");

$table["body"][]=array(
	1,
	2,
	5,
	6,
	0,
	0,
	0
);
$table["body"][]=array(
	1,
	2,
	3,
	4,
	0,
	0,
	0
);
$table["body"][]=array(
	1,
	2,
	3,
	4,
	0,
	0,
	0
);
$table["body"][]=array(
	1,
	0,
	0,
	0,
	0,
	0,
	0
);
$formulas=array(
	"E"=>"SUM(0:3)",
	"F"=>"SUM(0:4)",
	"G"=>"MULTIPLY(0:4)",
);
if(isset($_GET["list"])){
	foreach($table["body"] as $x=>$y) {
		foreach ($y as  $a => $b){
			$table=parser($a,$x,$table,$formulas);
		}
		echo "\n";
	}
	die(json_encode($table));
}

if(isset($_GET['value'])){

	echo $table["body"][$_POST["x"]][$_POST["y"]];
}



function parser($x,$y,$table,$formulas){
	if(isset($table['header'][$x]['name'])){
		if(isset($formulas[$table['header'][$x]['name']])){
			$getFormula=explode("(",$formulas[$table['header'][$x]['name']]);
			switch($getFormula[0]){
				case 'SUM':
					$pattern = '/SUM\((.*)\)/';
					preg_match($pattern, $formulas[$table['header'][$x]['name']], $res);
					if($params=explode(":",$res[1])){
						$sum = 0;
						for($i=$params[0]; $i<=$params[1]; $i++ ){
							$sum+=$table['body'][$y][$i];
						}
						$table["body"][$y][$x]=$sum;
					}
				break;
				case 'MULTIPLY':
					$pattern = '/MULTIPLY\((.*)\)/';

					preg_match($pattern, $formulas[$table['header'][$x]['name']], $res);
					if($params=explode(":",$res[1])){

						$multiply = 1;
						$sum = 0;
						for($i=$params[0]; $i<=$params[1]; $i++ ){
							$multiply*=$table['body'][$y][$i];
							$sum+=$table['body'][$y][$i];
						}
						if($sum === 0){
							$table["body"][$y][$x]=0;
						}else{
							$table["body"][$y][$x]=$multiply;
						}

					}
				break;
			}



		}
	}
	return $table;
}

?>