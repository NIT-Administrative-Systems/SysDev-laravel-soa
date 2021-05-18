<?php

namespace Northwestern\SysDev\SOA\Auth\Entity;

use Illuminate\Support\Arr;

class ActiveDirectoryUser implements OAuthUser
{
    /** @var string */
    protected $netid;

    /** @var string */
    protected $email;

    /** @var string */
    protected $userPrincipalName;

    /** @var string */
    protected $displayName;

    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var array */
    protected $rawData;

    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;

        $this->netid = strtolower(Arr::get($this->rawData, 'mailNickname'));
        $this->email = Arr::get($this->rawData, 'mail');
        $this->userPrincipalName = Arr::get($this->rawData, 'userPrincipalName');
        $this->displayName = Arr::get($this->rawData, 'displayName');
        $this->firstName = Arr::get($this->rawData, 'givenName');
        $this->lastName = Arr::get($this->rawData, 'surname');
    }

    /**
     * Always lower case.
     *
     * @return string
     */
    public function getNetid()
    {
        return $this->netid;
    }

    /**
     * Return NU email address.
     *
     * Not guaranteed to have a value for all netIDs.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * The Azure ActiveDirectory identifier.
     *
     * @return string
     */
    public function getUserPrincipalName()
    {
        return $this->userPrincipalName;
    }

    /**
     * The user's preferred full name.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * The full OAuth2 response, with all fields & tokens.
     *
     * @return array
     */
    public function getRawData()
    {
        return $this->rawData;
    }
}