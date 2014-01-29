<?php
/*******
	So wäre es vielleicht am besten gewesen.
 ***/


class Register
{
	static public $coll;
	
	static public function fuckMyFace($ref)
	{
		if(null == self::$coll){
			self::$coll = array();
		}
		self::$coll[$ref::KEY] = $ref;
	}
	
	static public function getReference($key)
	{
		if(array_key_exists($key, self::$coll)){
			return self::$coll[$key];
		}
		return null;
	}	
	
}

class SuperClass
{
	public function __construct(){
		Register::fuckMyFace($this);
	}
}

class A extends SuperClass
{
	const KEY = "A";

	public function fuckYourFace()
	{
		print "Fuck your face\n";
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
	
	Register::getReference("A")->fuckYourFace();
}

main();
