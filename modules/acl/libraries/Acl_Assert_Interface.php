<?php

interface Acl_Assert_Interface
{
    public function assert(Acl $acl, $role = null, $resource = null, $privilege = null);
}
