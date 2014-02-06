<?php
/*
 Supidupi Funkidub Very Important Access Control List Based Class Implementation
 <abstract> everything *can have a descent policy 
	: current *user has a role and such.
 The Very Important Delegate (VID) delegates.
 */

include("iacl.php");
include("policy.php");
include("acl.php");
include("roles.php");
include("delegateaggregator.php");

class VampaPoodoh implements IACL
{
	private $policy; 
	private $acl; 

	public function __construct()
	{
		// diss class has a policy like diss:
		$this->adaptPolicy(new Policy(Roles::KAKAMAN));
		
		// diss class has a user like diss:
		// $this->setACL(new ACL(Roles::KAKAMAN));
		$this->setACL(new ACL(Roles::MONKEY));
		// $this->setACL(new ACL(Roles::MONKEYMONKEY));
	
		//
		$task = new DelegateAggregator();
		$task->addDMethod("test1st", Roles::MONKEY);
		$task->addDMethod("test2nd", Roles::KAKAMAN);
		
		// 
		$d = $task->getMethodIndex($this->getRole());
		if(in_array($d, get_class_methods($this))){
			$this->$d(); 
		}
	}

	// interface method impl
	public function adaptPolicy($policy)
	{
		$this->policy = $policy;
	}
	
	public function setACL($acl)
	{
		$this->acl = $acl;
	}

	public function getACLACL()
	{
		return $this->acl;
	}

	public function getRole()
	{
		return $this->acl->currentRole;
	}

	public function test1st()
	{
		// enabledisable "save" button
		print "Yuhee, method for the monkeyman." . PHP_EOL;
	}

	public function test2nd()
	{
		// toggledisable "save" button
		print "Yuhhe, method for the kakaman!" . PHP_EOL;
	}
}

class SiteClassAdapterFilterImpl extends VampaPoodoh
{
}

function main(){ new SiteClassAdapterFilterImpl(); } main();
