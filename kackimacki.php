<?php
/*******
	So wäre es vielleicht am besten gewesen.
	Weiterhin verbirgt sich uns der Sinn.
	Keep it real; a handjob and a hot meal.
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

class FuckApp
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
		else {
			self::$coll[$key] = $res = new $key();		
		}
		return $res;
	}	
}

function main()
{
	FuckApp::get("A")->testMethod1st();
	FuckApp::get("A")->testMethod2nd();
	print 'FuckApp::get("A") === FuckApp::get("A")' . PHP_EOL;
	print (FuckApp::get("A") === FuckApp::get("A")) . PHP_EOL;
}

main();

