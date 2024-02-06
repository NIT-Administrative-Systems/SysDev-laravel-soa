<?php

namespace Northwestern\SysDev\SOA\Auth\Entity;

interface OAuthUser
{
    public function getToken();

    public function getNetid();

    public function getEmail();

    public function getDisplayName();

    public function getRawData();
}
