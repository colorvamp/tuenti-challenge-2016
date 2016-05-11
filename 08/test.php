<?php
	$_map    = new _map();
	$_map->map = json_decode(file_get_contents('map'),1);

	echo substr(implode( '',$_map->map[-37] ),2,-1).PHP_EOL;
	$tmp = [];
	foreach( $_map->map as $y=>$rows ){$tmp[] = $_map->map[$y][43];}
	echo substr(implode( '',$tmp ),2,-1).PHP_EOL;
	/* Reverse */
	echo substr(implode( '',array_reverse($_map->map[43]) ),2,-1).PHP_EOL;
	$tmp = [];
	foreach( $_map->map as $y=>$rows ){$tmp[] = $_map->map[$y][-37];}
	echo substr(implode( '',array_reverse($tmp) ),2,-1).PHP_EOL;

	$_map->paintd_final();
exit;
print_r($_dwarfs->getPos());
	//$_map->paintd(3,3);
print_r($path);
exit;
	if( !$path ){
		echo 'Sin movimientos';
		exit;
	}

	$move = array_shift($path);
	$pos = $_dwarfs->getPos();
	$p['x'] = $move['x']-$pos['x'];
	$p['y'] = $move['y']-$pos['y'];
	$k = false;
	if( $p['x'] == 0 && $p['y'] == -1 ){$k = 'u';}
	if( $p['x'] == 0 && $p['y'] == 1 ){$k = 'd';}
	if( $p['x'] == 1 && $p['y'] == 0 ){$k = 'r';}
	if( $p['x'] == -1 && $p['y'] == 0 ){$k = 'l';}
	if( !$k ){
		echo 'Sin tecla';
		exit;
	}
print_r($move);
	print_r($p);
	print_r($k);


	$_map->paint();
exit;
