<?php

namespace Northwestern\SysDev\SOA\Auth;

trait SSOLoggable
{
    public function identifiers()
    {
        return '';

        # If you have the netid attribute you might replace this method with:
        # return 'netid='.$this->netid

        # Or if you want to pass multiple identifiers:
        # return 'netid='.$this->netid.' firstname='.$this->name.' lastname='.$this->lastname
        # => "netid=jmm222 firstname=Jimmy lastname=McMillan"
    }
}
