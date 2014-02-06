<?php

class DelegateAggregator
{
	public $ary;
	
	public function __construct()	
	{
		$this->ary = array();	
	}
		
	public function addDMethod($method, $role)
	{
		$this->ary[$role] = $method;
	}

	public function getMethodIndex($role)
	{
		if(array_key_exists($role, $this->ary)){
			return $this->ary[$role];
		}
	}
}
