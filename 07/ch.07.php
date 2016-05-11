#!/usr/bin/php
<?php
	/* Challenge 7 - Tiles
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */
	$i = file('php://stdin');
	array_shift($i);

	$values = ['.'=>0];

	/* INI-Calc matrix values */
	$tmp = range('A','Z');
	$tmp = array_flip($tmp);
	$tmp = array_map(function($n){return $n + 1;},$tmp);
	$values += $tmp;

	$tmp = range('a','z');
	$tmp = array_flip($tmp);
	$tmp = array_map(function($n){return ($n + 1) * -1;},$tmp);
	$values += $tmp;
	/* END-Calc matrix values */

	$test = 0;while($size = array_shift($i)){$test++;
		$size  = explode(' ',trim($size));
		$lines = $size[0];
		$blob  = '';
		while( $lines-- ){
			$blob .= array_shift($i);
		}

		//if( $test < 2 ){continue;}
		$blob = trim($blob);
		$matrix = [];

		/* INI-Multiply By 2 */
		$j = 2;
		$tmp = '';
		while( $j-- ){
			$lines = explode(PHP_EOL,$blob);
			foreach( $lines as $k=>$line ){
				$line = $line.$line;
				$tmp .= $line.PHP_EOL;
			}
		}
		$tmp  = trim($tmp);
		$blob = $tmp;
		/* END-Multiply By 2 */

		/* INI-Index Matrix */
		$lines = explode(PHP_EOL,$blob);
		foreach( $lines as $k=>$line ){
			$arr = str_split($line);
			foreach( $arr as $j=>$letter ){
				$matrix[$k][$j] = $values[$letter];
			}
		}
		/* END-Index Matrix */

		$r = cornerCases($matrix);
		if( $r !== false ){echo 'Case #'.$test.': '.$r.PHP_EOL;continue;}
		$max = find_max_sum($matrix);
		echo 'Case #'.$test.': '.$max.PHP_EOL;
	}


	function cornerCases( &$input = [] ){
		foreach( $input as $x ){
			$sum = array_sum($x);
			if( $sum > 0 ){return 'INFINITY';}
		}
		foreach( $x as $k=>$y ){
			$sum = 0;
			foreach( $input as $line ){
				$sum += $line[$k];
			}
			if( $sum > 0 ){return 'INFINITY';}
		}

		return false;
	}

	function _kadane( &$input = [], $n, &$x1, &$x2, &$max ){  
		$max = 0;
		$cur = 0;  
		$x1  = $x2 = 0;  
		$lx1 = 0;  
		for( $i = 0; $i < $n; $i++ ){  
			$cur = $cur+$input[$i];
			if( $cur > $max ){  
				$max = $cur;
				$x2 = $i;
				$x1 = $lx1;
			}
			if( $cur < 0 ){  
				$cur = 0;
				$lx1 = $i + 1;
			}
		}
	}

	function find_max_sum( &$input = [] ){
		$tmp = [];
		$x1 = $x2 = false;
    		$fx1 = $fx2 = $fy1 = $fy2 = $max_sum = $cur = -1;  
		$m = count($input);
		$n = count($input[0]);
  
		for( $i = 0; $i < $m; $i++ ){
			for( $k = 0; $k < $n; $k++ ){$tmp[$k] = 0;}
  
			for( $j = $i; $j < $m; $j++ ){  
				for( $k = 0; $k < $n; $k++ ){
					$tmp[$k] += $input[$j][$k];
				}
            			_kadane($tmp,$n,$x1,$x2,$cur);  
  
				if( $cur > $max_sum ){
					$fy1 = $x1;
					$fy2 = $x2;
					$fx1 = $i;
					$fx2 = $j;
					$max_sum = $cur;
				} 
			}  
		}  
    
		//echo 'max Sum = '.$max_sum.' from ('.$fx1.','.$fy1.') to ('.$fx2.','.$fy2.')'.PHP_EOL;  
		return $max_sum;
	}

	function paintMatrix( &$matrix = [] ){
		foreach( $matrix as $x ){
			echo '┼';
			foreach( $x as $y ){
				echo '────┼';
			}
			echo PHP_EOL;
			echo '|';
			foreach( $x as $y ){
				echo str_pad($y,4,' ',STR_PAD_LEFT).'|';
			}
			echo str_pad(array_sum($x),4,' ',STR_PAD_LEFT).'|';
			echo PHP_EOL;
		}
		echo '┼';
		foreach( $x as $y ){
			echo '────┼';
		}
		echo PHP_EOL;

		echo '|';
		foreach( $x as $k=>$y ){
			$sum = 0;
			foreach( $matrix as $line ){
				$sum += $line[$k];
			}
			echo str_pad($sum,4,' ',STR_PAD_LEFT).'|';
		}
		echo PHP_EOL;
	}


	
