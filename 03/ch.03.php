#!/usr/bin/php
<?php
	/* Challenge 3 - YATM Microservice
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */
	$i = file('php://stdin');
	$blob = implode(PHP_EOL,$i);
	//$code = yaml_parse(file_get_contents('exampleInput'));
	$code = yaml_parse($blob);
	//print_r($code);exit;

	foreach( $code['tapes'] as $k=>$input ){
		$state   = 'start';
		$pointer = 0;

		//while( isset($input[$pointer]) ){
		while( 1 ){
			$char = isset($input[$pointer]) ? $input[$pointer] : ' ';

			if( !isset($code['code'][$state][$char]) ){
				var_dump($char);
				print_r($code['code'][$state]);
				exit;
			}
			$op = $code['code'][$state][$char];
			if( isset($op['write']) ){
				$input[$pointer] = $op['write'];
			}else{
				$input[$pointer] = $input[$pointer];
			}
			if( isset($op['move']) ){
				if( $op['move'] == 'right' ){$pointer++;}
				elseif( $op['move'] == 'left' ){$pointer--;}
			}
			if( isset($op['state']) ){
				$state = $op['state'];
				//echo $state.PHP_EOL;
				if( $state == 'end' ){break;}
			}
		}
		echo 'Tape #'.$k.': '.$input.PHP_EOL;	
	}

