#!/usr/bin/php
<?php
	/* Challenge 1 - Team Lunch
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */
	$i = file('php://stdin');
	array_shift($i);

	$k = 0;while($line = array_shift($i)){$k++;
		$line = trim($line);
		//echo $line.PHP_EOL;
		if( !$line ){echo 'Case #'.$k.': 0'.PHP_EOL;continue;}
		if( $line > 0 && $line < 3 ){$line = 3;}
		$line -= 2;
		echo 'Case #'.$k.': '.ceil($line/2).PHP_EOL;
	}
