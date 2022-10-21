<?php

use Illuminate\Foundation\Auth\User as UserModel;
use Illuminate\Http\Request;
use MOIREI\EventTracking\Contracts\EventUser;
use MOIREI\EventTracking\Contracts\EventUserProxy;
use MOIREI\EventTracking\EventTracking;
use MOIREI\EventTracking\Objects\User as UserObject;

uses()->group('event-tracking-user');

beforeEach(function () {
    $request = new Request();
    $this->eventTracking = new EventTracking($request);
});

it('should accept user object', function () {
    $user = new UserObject();

    $this->eventTracking->user($user);

    expect($this->eventTracking->getCache('$user'))->toEqual($user);
});

it('should accept EventUserProxy instance', function () {
    $userObject = new UserObject();
    $user = new class($userObject) extends UserModel implements EventUserProxy
    {
        public function __construct(protected $user)
        {
        }

        public function getEventUser(): UserObject
        {
            return $this->user;
        }
    };

    $this->eventTracking->user($user);

    expect($this->eventTracking->getCache('$user'))->toEqual($userObject);
});

it('should accept EventUser instance', function () {
    $name = \Faker\Factory::create()->name();

    $user = new class($name) extends UserModel implements EventUser
    {
        public function __construct(protected $name)
        {
        }

        public function getId()
        {
            return '';
        }

        public function getName()
        {
            return $this->name;
        }

        public function getFirstName()
        {
            return '';
        }

        public function getLastName()
        {
            return '';
        }

        public function getEmail()
        {
            return '';
        }

        public function getCreatedDate()
        {
            return '';
        }

        public function getProperties()
        {
            return [];
        }
    };

    $this->eventTracking->user($user);

    expect($this->eventTracking->getCache('$user')->name)->toEqual($name);
});
