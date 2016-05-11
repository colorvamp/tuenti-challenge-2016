#!/usr/bin/php
<?php
	/* Challenge 5 - Hangman
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */

	class _hangman{
		public $ip   = '52.49.91.111';
		public $port = '9988';
		public $fp = false;
		public $cr = "\r\n";
		function connect(){
			$this->fp = fsockopen($this->ip,$this->port,$errno,$errstr,30);
			if( !$this->fp ){echo "$errstr ($errno)<br />\n";exit;}
			stream_set_blocking($this->fp,0);
			stream_set_blocking(STDIN,0);
			$this->write('');
		}
		function write($text = ''){
			fwrite($this->fp,$text.$this->cr);
			usleep(150000);/* Living with wifi is hard */
		}
		function read(){
			$blob = '';
			//$maxreads = 10;
			while( ($buffer = fgets($this->fp,1024)) ){
				//if( preg_match('!Level: 1 - Time remaining: 0 s.!') ){}
				//if( strlen($buffer) == 1 ){break;}
				$blob .= $buffer;
			}//*/

			return $blob;
		}
		function close(){
			fclose($this->fp);
		}
		function parse(){
			//sleep(1);
			$blob = $this->read();
			echo $blob.PHP_EOL;

			if( preg_match('!Your ([^ ]+) key is: ([^ \n\r\t]+)!',$blob,$m) ){
				print_r($m);
				if( $m[1] == 'submit' ){exit;}
				return true;
			}
			if( preg_match('!Level [0-9]+ cleared\!!',$blob,$m) ){
				print_r($m);
				return true;
			}
			if( preg_match('!GAME OVER!',$blob) ){
				echo 'GAME OVER';
				$this->close();
				exit;
			}

			if( ($test = preg_split('!Level: [0-9]+ - Time remaining: [0-9]+ s.!',$blob)) ){
				$blob = isset($test[1]) ? $test[1] : $test[0];
			}

			$screen = [];
			$blob = explode(PHP_EOL,$blob);
			if( !isset($blob[9]) ){return true;}
			$screen['chars'] = array_values(array_filter(array_diff(str_split($blob[9]),[' '])));
			$screen['chars.num'] = count($screen['chars']);
print_r($screen);
			return $screen;
		}
	}

	class _dict{
		public $dictByLength = [];
		function index(){
			$dict = file('words.txt');
			$this->dictByLength = [];

			foreach( $dict as $k=>$word ){
				$word = trim($word);
				$len  = strlen($word);
				$this->dictByLength[$len][] = $word;
				unset($dict[$k]);
			}
			unset($len);
		}
		function getWords($length = 1,$match = ['_','_','_','_'],$exclude = []){
			if( !is_int($length) ){var_dump($length);exit;}

			$candidates = [];
			foreach( $this->dictByLength[$length] as $word ){
				foreach( $match as $k=>$char ){
					if( $char == '_' ){
						if( in_array($word[$k],$exclude) ){continue 2;}
						continue;
					}
					if( $word[$k] != $char ){continue 2;}
				}
				$candidates[] = $word;
			}
			return $candidates;
		}
		function getLetters(&$pool = [],$exclude = []){
			if( $exclude ){$exclude = array_unique(array_diff($exclude,['_']));}
			$tmp = [];
			foreach( $pool as $k=>$word ){
				$letters = str_split($word);
				$letters = array_unique($letters);
				foreach( $letters as $letter ){
					if(  in_array($letter,$exclude) ){continue;}
					$type = 'vowel';
					if( !in_array($letter,['A','E','I','O','U']) ){$type = 'cons';}
					if( !isset($tmp[$type][$letter]) ){$tmp[$type][$letter] = 0;}
					$tmp[$type][$letter]++;
				}
			}
			if( isset($tmp['vowel']) ){arsort($tmp['vowel']);}
			if( isset($tmp['cons']) ){arsort($tmp['cons']);}
			return $tmp;
		}
		function selectLetter(&$pool = [],$match = ['_','_','_','_']){
			/* Lets do it more fun */
			//return ( isset($pool['vowel']) && $pool['vowel'] ) ? key($pool['vowel']) : key($pool['cons']);

			if( !isset($pool['vowel']) && !isset($pool['cons']) ){return false;}

			$key = false;
			$current = 0;
			if( isset($pool['vowel']) && current($pool['vowel']) > $current ){$key = key($pool['vowel']);$current = current($pool['vowel']);}
			if( isset($pool['cons']) && current($pool['cons']) > $current ){$key = key($pool['cons']);$current = current($pool['cons']);}
			return $key;
			

			$count = array_count_values($match);

			$type = false;
			/* If we didnt matched any vowel yet, keeps trying */
			if( isset($pool['vowel']) && $pool['vowel'] && isset($count['_']) && count($match) == $count['_'] ){$type = 'vowel';}
			if( !$type && !isset($pool['vowel']) || !$pool['vowel'] ){$type = 'cons';}
			if( !$type && !isset($pool['cons']) || !$pool['cons'] ){$type = 'vowel';}
			if( !$type ){
				//FIXME: no me fio mucho de este porcentaje
				$perc1 = (count($pool['vowel'])/5);
				$perc2 = (count($pool['cons'])/21);
				$type = $perc1 > $perc2 ? 'vowel' : 'cons';
			}
			return key($pool[$type]);
		}
	}

	$_dict = new _dict();
	$_dict->index();

	//$candidates = $_dict->getWords(5,['_','_','_','O','_'],['O']);
	//print_r($candidates);
	//exit;

	$excluded = [];

	$_hangman = new _hangman();
	$_hangman->connect();
	$screen = $_hangman->parse();

	$i = 1500;
	while( $i-- ){
		$letter = '';
		$candidates = $_dict->getWords($screen['chars.num'],$screen['chars'],$excluded);
		if( $candidates ){
			//print_r($candidates);
			$tmp = $_dict->getLetters($candidates,$excluded);
			$letter = $_dict->selectLetter($tmp,$screen['chars'],$screen['chars']);
			$excluded[] = $letter;
			echo 'Se ha seleccionado '.$letter.PHP_EOL;
		}

		$_hangman->write($letter);
		$screen = $_hangman->parse();
		while( $screen === true ){
			$excluded = [];
			$_hangman->write('');
			$screen = $_hangman->parse();
		}
		//print_r($screen);
	}

	$_hangman->close();

