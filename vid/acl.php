<?php

class ACL
{
	public function __construct($role)
	{
		$this->currentRole = $role;
	}
	public $currentRole;
}
