<?php
/*******
	So wäre es vielleicht am besten gewesen.
	Der Sinn verweigert sich uns.
  ***/


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

main();
