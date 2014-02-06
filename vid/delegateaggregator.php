<?php

class DelegateAggregator
{
	public $ary;
	
	public function __construct()	
	{
		$this->ary = array();	
	}
		
	public function addDelegate($method, $role)
	{
		$this->ary[$role] = $method;
	}

	public function delegate($role)
	{
		if(array_key_exists($role, $this->ary)){
			return $this->ary[$role];
		}
	}
}
