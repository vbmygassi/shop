<?php

interface IACLFuck
{
	public function adaptPolicy($policy);
	public function setACL($acl);
}
