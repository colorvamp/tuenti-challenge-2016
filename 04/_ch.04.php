#!/usr/bin/php
<?php
	/* Challenge 4 - Hadouken!
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */
	$i = file('php://stdin');
	array_shift($i);

	$combos = [
		 ['L','LD','D','RD','R','P']
		,['D','RD','R','P']
		,['R','D','RD','P']
		,['D','LD','L','K']
		,['R','RD','D','LD','L','K']
	];
	foreach( $combos as $k=>$combo ){
		$cpy = $combo;
		array_pop($cpy);
		$combos[$k] = [
			 'orig'=>$combo
			,'complete'=>'-'.implode('-',$combo).'-'
			,'almost'=>'-'.implode('-',$cpy).'-'
			,'length'=>count($combo)
			,'count'=>0
		];
	}
	uasort($combos,function($a,$b){
		if($a['length'] == $b['length']){return 0;}
		return ($a['length'] > $b['length']) ? -1 : 1;
	});

	$linec = 0;while($line = array_shift($i)){$linec++;
		//if( $linec < 2 ){continue;}
		$line = '-'.trim($line).'-';
		//echo $line.PHP_EOL;
		$cache = $combos;

		foreach( $cache as $j=>$combo ){
			//echo $combo['almost'].PHP_EOL;
			$continue = false;
			$pos      = 0;

			/*$r = preg_match_all('!'.$combo['almost'].'((?<end>[A-Z]+)\-|$)!',$line,$m);
			$last  = end($combo['orig']);
			$total = count($m[0]);
			foreach( $m['end'] as $k=>$end ){
				if( $end === $last ){$total--;}
			}//*/

			$cache[$j]['pos'] = [];
			do{
				if( ($pos = strpos($line,$combo['almost'],$pos)) === false ){break;}
				if( (strpos($line,$combo['complete'],$pos)) === $pos ){$pos += strlen($combo['complete'])-1;$continue = true;continue;}
				//echo substr($line,$pos,strlen($combo['complete'])).PHP_EOL;
				$cache[$j]['pos'][] = [$pos,$pos+strlen($combo['almost'])];
				$continue = true;
				$pos += 1;
			}while( $continue );
		}


		/* INI-Cleanup */
		foreach( $cache as $combo1 ){
			foreach( $cache as $y=>$combo2 ){
				if( !isset($combo1['pos'],$combo2['pos']) ){continue;}
				if( $combo1['almost'] === $combo2['almost'] ){continue;}
				if( strpos($combo1['almost'],$combo2['almost']) === false ){continue;}
				foreach( $combo2['pos'] as $x=>$pos ){
					foreach( $combo1['pos'] as $mpos ){
						if( $pos[0] >= $mpos[0] && $pos[1] <= $mpos[1] ){
							unset($cache[$y]['pos'][$x]);
						}
					}
				}
			}
		}
		/* END-Cleanup */
if( $linec == 98 ){
print_r($cache);
exit;
}

		$sumByCombos = [];
		foreach( $cache as $combo ){
			if( !isset($combo['pos']) ){continue;}
			if( !isset($sumByCombos[$combo['length']]) ){$sumByCombos[$combo['length']] = 0;}
			$sumByCombos[$combo['length']] += count($combo['pos']);
		}
		$sum = array_sum($sumByCombos);

		echo 'Case #'.$linec.': '.$sum.PHP_EOL;
	}


