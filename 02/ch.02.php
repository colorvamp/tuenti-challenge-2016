#!/usr/bin/php
<?php
	/* Challenge 2 - The Voynich Manuscript
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */
	$i = file('php://stdin');
	array_shift($i);
	$corpus = file_get_contents('corpus.txt');
	$words  = explode(' ',$corpus);

	$k = 0;while($line = array_shift($i)){$k++;
		$line = trim($line);
		$coords = explode(' ',$line);

		$chunk  = array_slice($words,$coords[0]-1,($coords[1]-$coords[0])+1);
		$values = array_count_values($chunk);
		arsort($values);

		$values = array_slice($values,0,3);
		//print_r($chunk);
		$exit = 'Case #'.$k.': ';
		foreach( $values as $name=>$value ){
			$exit .= $name.' '.$value.',';
		}
		$exit = substr($exit,0,-1);

		//echo $line.PHP_EOL;
		echo $exit.PHP_EOL;
	}
