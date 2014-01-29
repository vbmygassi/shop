<?php
/*******
	So wäre es vielleicht am besten gewesen.
	Der Sinn verweigert sich uns.
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
		else{
			self::$coll[$key] = $res = new $key;		
		}
		return $res;
	}	
}

function main()
{
	FuckApp::get("A")->testMethod1st();
	FuckApp::get("A")->testMethod2nd();
	print 'FuckApp::get("A") === FuckApp::get("A")' . PHP_EOL;
	print(FuckApp::get("A") === FuckApp::get("A"));
	print PHP_EOL;
}

main();

/*
class Register
{
	static public $coll;
	
	static public function add($ref)
	{
		if(null == self::$coll){
			self::$coll = array();
		}
		self::$coll[$ref::KEY] = $ref;
	}
	
	static public function get($key)
	{
		if(array_key_exists($key, self::$coll)){
			return self::$coll[$key];
		}
		return null;
	}	
	
}

class SuperClass
{
	public function __construct()
	{
		Register::add($this);
	}
}

class A extends SuperClass
{
	const KEY = "A";

	public function testMethod()
	{
		print "testMethod()\n";
	}
}

class B extends SuperClass
{
	const KEY = "B";
}

function main()
{
	$a = new A();
	$b = new B();
	
	Register::get("A")->testMethod();
}
*/

