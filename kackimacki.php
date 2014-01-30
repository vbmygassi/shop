<?php
/*******
  Hach, wer will das noch lesen
	***/

class A
{
	public function __construct()
	{
		if(false){
			throw(new Exception("NO"));
		}
	}

	public function testMethod1st()
	{
		print "A::testMethod1st()" . PHP_EOL;
	}

	public function testMethod2nd()
	{
		print "A::testMethod2nd()" . PHP_EOL;
	}
}

class B
{
}

class App
{
	static private $coll;

	static public function get($key)
	{
		$res = null;
		if(null == self::$coll){
			self::$coll = array();
		}
		if(array_key_exists($key, self::$coll)){
			$res = self::$coll[$key];
		}
		else{
			self::$coll[$key] = $res = new $key();		
		}
		return $res;
	}	
}

function main()
{
	App::get("A")->testMethod1st();
	App::get("A")->testMethod2nd();
	print 'App::get("A") === App::get("A")' . PHP_EOL;
	print (App::get("A") === App::get("A")) . PHP_EOL;
}

main();

