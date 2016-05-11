#!/usr/bin/php
<?php
	/* Challenge 8 - Labyrinth
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */

	//include('test.php');exit;
	$_map    = new _map();
	$_laby   = new _laby();
	$_laby->connect();
	$viewport = ['x'=>0,'y'=>0];

	$final = false;//Put on to resolve (when /run/shm/map is completed)
	if( $final ){
		/* Echo this to a file */
		$_map    = new _map();
		$_map->map = json_decode(file_get_contents('/run/shm/map'),1);

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
	}

	if( file_exists('/run/shm/map') ){
		/* Resume map */
		$_map->map = json_decode(file_get_contents('/run/shm/map'),1);
		$_dwarfs = new _dwarfs();
		$_dwarfs->matrix = $_map->map;
		$_dwarfs->findPos();
		$pos = $_dwarfs->getPos();
		$_map->map[$pos['y']][$pos['x']] = '_';
		$_map->map[3][3] = 'x';
	}

	$i = 50000;
	while( $i-- ){
		$blob = $_laby->read();
		$r = $_map->parse($blob,$viewport['x'],$viewport['y']);
		if( $r === 'retry' ){
			echo 'retry';
			$blob = $_laby->read();
			$r = $_map->parse($blob,$viewport['x'],$viewport['y']);
			if( $r === 'retry' ){echo 'mal asunto';exit;}
		}

		$_dwarfs = new _dwarfs();
		$_dwarfs->matrix = $_map->map;
		$path = $_dwarfs->start();
		if( !$path ){
			echo 'Sin movimientos';
			exit;
		}

		/*if( count($path) > 1 ){
			echo 'multiples movimientos';
			print_r($path);
			exit;
		}*/

		$move = array_shift($path);
		$pos = $_dwarfs->getPos();
		system('clear');
		$_map->paintd($pos['x'],$pos['y']);
		$p['x'] = $move['x']-$pos['x'];
		$p['y'] = $move['y']-$pos['y'];
		$k = false;
		if( $p['x'] == 0 && $p['y'] == -1 ){$k = 'u';}
		if( $p['x'] == 0 && $p['y'] == 1 ){$k = 'd';}
		if( $p['x'] == 1 && $p['y'] == 0 ){$k = 'r';}
		if( $p['x'] == -1 && $p['y'] == 0 ){$k = 'l';}
		$viewport['x'] += $p['x'];
		$viewport['y'] += $p['y'];
		if( !$k ){
			echo json_encode($_map->map);
			print_r($move);
			print_r($pos);
			print_r($path);
			echo 'Sin tecla'.PHP_EOL;
			exit;
		}
		echo 'Pulsado ('.$i.'): '.$k.PHP_EOL;
		$_laby->write($k);
		file_put_contents('/run/shm/map',json_encode($_map->map));
	}
//echo json_encode($_map->map);
exit;
	
	class _map{
		public $map = [];
		public $lastparse = '';
		function store($matrix = [],$xt = 0,$yt = 0){
			foreach( $matrix as $y=>$row ){
				foreach( $row as $x=>$cell ){
					if( $cell == 'o' ){$cell = ' ';}
					//if( $cell == 'x' ){$cell = '_';}
					if( $cell == ' ' && isset($this->map[$y+$yt][$x+$xt]) ){
						if( $this->map[$y+$yt][$x+$xt] == 'x' ){$cell = '_';}
						else{continue;}
					}
					$this->map[$y+$yt][$x+$xt] = $cell;
				}
				ksort($this->map[$y+$yt]);
			}
			ksort($this->map);
		}
		function parse( $blob = '',$x = false,$y = false ){
			$blob = preg_replace('![\n]+$!','',$blob);
			if( $blob === $this->lastparse ){return false;}
			$this->lastparse = $blob;
			$matrix = [];

			$lines = explode(PHP_EOL,$blob);
			$count = count($lines);
			if( $count == 1 ){return 'retry';}
			if( $count != 7 ){
echo 'algo raro pasa';
print_r($lines);
exit;
}
			foreach( $lines as $k=>$line ){
				$matrix[$k] = str_split($line);
			}
			if( $x !== false && $y !== false ){$this->store($matrix,$x,$y);}
			return $matrix;
		}
		function paint( $matrix = [] , $vx = false , $vy = false ){
			$limit = 6;
			if( !$matrix ){$matrix = $this->map;}
			foreach( $matrix as $y=>$rows ){
				if( $vy !== false && ( $y < ($vy - $limit) || $y > ($vy + $limit) ) ){continue;}
				$footer = 0;
				echo '┼';
				foreach( $rows as $x=>$v ){
					if( $vx !== false && ( $x < ($vx - $limit) || $x > ($vx + $limit) ) ){continue;}
					$footer += 1;
					echo '───┼';
				}
				echo PHP_EOL;
				echo '|';
				foreach( $rows as $x=>$v ){
					if( $vx !== false && ( $x < ($vx - $limit) || $x > ($vx + $limit) ) ){continue;}
					if( $v == ' ' ){echo $x.'.'.$y.'|';continue;}
					echo ' '.$v.' |';
				}
				echo PHP_EOL;
			}
			if( isset($footer) ){
				echo '┼';
				while( $footer-- ){
					echo '───┼';
				}
				echo PHP_EOL;
			}
		}
		function paintd( $vx = false , $vy = false ){
			$limit = 6;
			$xs = range($vx - $limit,$vx + $limit);
			$ys = range($vy - $limit,$vy + $limit);

			foreach( $ys as $y ){
				echo '┼';
				foreach( $xs as $x){echo '───┼';}
				echo PHP_EOL;

				echo '|';
				foreach( $xs as $x){
					$v = isset($this->map[$y][$x]) ? $this->map[$y][$x] : ' ';
					if( $v == '#' ){$v = "\033[31m".$v."\033[0m";}
					echo ' '.$v.' |';
				}
				echo PHP_EOL;
			}

			echo '┼';
			foreach( $xs as $x){echo '───┼';}
			echo PHP_EOL;
		}
		function paintd_final(){
			foreach( $this->map as $y=>$rows ){
				foreach( $rows as $x=>$v ){
					$v = isset($this->map[$y][$x]) ? $this->map[$y][$x] : ' ';
					echo $v;
				}
				echo PHP_EOL;
			}
			echo PHP_EOL;
		}
	}
	class _dwarfs{
		public $matrix = [];
		public $found  = false;
		public $path   = [];
		public $startX = false;
		public $startY = false;
		public $target = ' ';
		function findPos(){
			$this->startX = $this->startY = false;
			foreach( $this->matrix as $y=>$row ){
				if( ($x = array_search('x',$row)) !== false ){
					$this->startX = $x;
					$this->startY = $y;
					return true;
				}
			}
			return false;
		}
		function getPos(){
			return ['x'=>$this->startX,'y'=>$this->startY];
		}
		function start(){
			$r = $this->findPos();
			if( !$r ){echo 'Player not found';exit;}
			$this->move($this->startX+1,$this->startY);
			$this->move($this->startX-1,$this->startY);
			$this->move($this->startX,$this->startY+1);
			$this->move($this->startX,$this->startY-1);

			return $this->path;
		}
		function move($x,$y,$mov = 0,$visited = [],$path = []){
			if( $this->found && ($this->found <= $mov) ){return false;}
			if(  isset($visited[$y][$x]) ){return false;}
			if( !isset($this->matrix[$y][$x]) ){return false;}
			$visited[$y][$x] = 1;

			if( $this->matrix[$y][$x] == '#'){return false;}
			if( $this->matrix[$y][$x] == 'x' ){return false;}

			$path[] = ['x'=>$x,'y'=>$y];
			$mov++;
			if( $this->matrix[$y][$x] == $this->target && $mov ){
				$this->path  = $path;
				$this->found = $mov;
				return false;
			}

			$this->move($x+1,$y,$mov,$visited,$path);
			$this->move($x-1,$y,$mov,$visited,$path);
			$this->move($x,$y+1,$mov,$visited,$path);
			$this->move($x,$y-1,$mov,$visited,$path);
		}
	}

	class _laby{
		public $ip   = '52.49.91.111';
		public $port = '1986';
		public $fp   = false;
		public $cr   = "\n";
		function connect(){
			$this->fp = fsockopen($this->ip, $this->port, $errno, $errstr, 30);
			if( !$this->fp ){echo "$errstr ($errno)<br />\n";exit;}
			stream_set_blocking($this->fp,0);
        		stream_set_blocking(STDIN,0);
			usleep(150000);
		}
		function write($text = ''){
			fwrite($this->fp,$text.$this->cr);
			usleep(180000);
		}
		function read(){
			echo 'READ -----'.PHP_EOL;
			$blob = '';
			while( ($buffer = fgets($this->fp,128)) ){
				$blob .= $buffer;
			}
			return $blob;
		}
	}

