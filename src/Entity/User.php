<?php

namespace League\OAuth2\Client\Entity;

class User
{
    protected $uid;
    protected $openid;
    protected $nickname;
    protected $sex;
    protected $name;
    protected $firstName;
    protected $lastName;
    protected $province;
    protected $city;
    protected $country;
    protected $privilege;
    protected $email;
    protected $location;
    protected $description;
    protected $imageUrl;
    protected $urls;
    protected $gender;
    protected $locale;
    protected $subscribe;
    protected $subscribe_time;
    protected $unionid;

    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new \OutOfRangeException(sprintf(
                '%s does not contain a property by the name of "%s"',
                __CLASS__,
                $name
            ));
        }

        return $this->{$name};
    }

    public function __set($property, $value)
    {
        if (!property_exists($this, $property)) {
            throw new \OutOfRangeException(sprintf(
                '%s does not contain a property by the name of "%s"',
                __CLASS__,
                $property
            ));
        }

        $this->$property = $value;

        return $this;
    }

    public function __isset($name)
    {
        return (property_exists($this, $name));
    }

    public function getArrayCopy()
    {
        return array(
            'uid' => $this->uid,
            'openid' => $this->openid,
            'nickname' => $this->nickname,
            'sex' => $this->sex,
            'name' => $this->name,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'province' => $this->province,
            'city' => $this->city,
            'country' => $this->country,
            'privilege' => $this->privilege,
            'email' => $this->email,
            'location' => $this->location,
            'description' => $this->description,
            'imageUrl' => $this->imageUrl,
            'urls' => $this->urls,
            'gender' => $this->gender,
            'locale' => $this->locale,
            'subscribe' => $this->subscribe,
            'subscribe_time' => $this->subscribe_time,
            'unionid' => $this->unionid,
        );
    }

    public function exchangeArray(array $data)
    {
        foreach ($data as $key => $value) {
            $key = strtolower($key);
            switch ($key) {
                case 'uid':
                    $this->uid = $value;
                    break;
                case 'openid':
                    $this->openid = $value;
                    break;
                case 'nickname':
                    $this->nickname = $value;
                    break;
                case 'sex':
                    $this->sex = $value;
                    break;
                case 'name':
                    $this->name = $value;
                    break;
                case 'firstname':
                    $this->firstName = $value;
                    break;
                case 'lastname':
                    $this->lastName = $value;
                    break;
                case 'province':
                    $this->province = $value;
                    break;
                case 'city':
                    $this->city = $value;
                    break;
                case 'country':
                    $this->country = $value;
                    break;
                case 'privilege':
                    $this->privilege = $value;
                    break;
                case 'email':
                    $this->email = $value;
                    break;
                case 'location':
                    $this->location = $value;
                    break;
                case 'description':
                    $this->description = $value;
                    break;
                case 'imageurl':
                    $this->imageUrl = $value;
                    break;
                case 'urls':
                    $this->urls = $value;
                    break;
                case 'gender':
                    $this->gender = $value;
                    break;
                case 'locale':
                    $this->locale = $value;
                    break;
                case 'subscribe':
                    $this->subscribe = $value;
                    break;
                case 'subscribe_time':
                    $this->subscribe_time = $value;
                    break;
                case 'unionid':
                    $this->unionid = $value;
                    break;
            }
        }

        return $this;
    }
}
