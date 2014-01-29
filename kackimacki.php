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

	public function testMethod()
	{
		print "A::testMethod()" . PHP_EOL;
	}
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
			$res = new $key();		
			self::$coll[$key] = $res;		
		}
		return $res;
	}	
}

function main()
{
	FuckApp::get("A")->testMethod();
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

