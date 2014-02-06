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
		$this->adaptPolicy(new Policy(Roles::MONKEY));
		// 	
		// diss class has a user like diss:
		// $this->setACL(new ACL(Roles::KAKAMAN));
		$this->setACL(new ACL(Roles::MONKEY));
		// $this->setACL(new ACL(Roles::MONKEYMONKEY));
		//
		$this->test();
		return true;
	}

	protected function test()
	{		 
		$task = new DelegateAggregatorSiteClassAdpaterFactoryImpl();
		$task->addDMethod("test1st", Roles::MONKEY);
		$task->addDMethod("test2nd", Roles::KAKAMAN);
		
		// checks against policy of runtime
		if($this->policy->role > $this->acl->currentRole){
			print "not allowed: tdschäck the policy" . PHP_EOL;
			return false;
		}
		
		// fetches very important delegate method	
		$d = $task->getMethodIndex($this->getRole());
		if(in_array($d, get_class_methods($this))){
			$this->$d(); 
		}
		else {
			print "no delegate for: " . $this->getRole() . PHP_EOL;
		}
		return true;
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
		print "Yuhee, task for the monkeyman." . PHP_EOL;
	}

	public function test2nd()
	{
		// toggledisable "save" button
		print "Yuhhe, task for the kakaman!" . PHP_EOL;
	}
}

class SiteClassAdapterFilterImpl extends VampaPoodoh{ }



function main(){ new SiteClassAdapterFilterImpl(); } main();
