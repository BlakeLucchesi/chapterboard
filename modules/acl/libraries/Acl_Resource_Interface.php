<?php

interface Acl_Resource_Interface
{
    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function get_resource_id();
}
