<?php

use MOIREI\EventTracking\Objects\User as UserObject;

uses()->group('user-object');

it('should receive unknown property', function () {
    $user = new UserObject();
    $user->id = '34';

    $user->type = 'user';

    expect($user->toArray())->toHaveKey('type');
    expect($user->toArray()['type'])->toEqual('user');
});

it('should receive unknown array access', function () {
    $user = new UserObject();
    $user->id = '34';

    $user['age'] = 100;

    expect($user->toArray())->toHaveKey('age');
    expect($user->toArray()['age'])->toEqual(100);
});

it('should fill object with values', function () {
    $user = new UserObject();

    $user->fill([
        'id' => 1,
        'age' => 100,
    ]);

    expect($user->id)->toEqual(1);
    expect($user['age'])->toEqual(100);

    expect($user->toArray())->toHaveKey('id');
    expect($user->toArray())->toHaveKey('age');

    expect($user->toArray()['id'])->toEqual(1);
    expect($user->toArray()['age'])->toEqual(100);
});

it('should receive input during create', function () {
    $user = new UserObject([
        'id' => 1,
        'age' => 100,
    ]);

    expect($user->id)->toEqual(1);
    expect($user['age'])->toEqual(100);

    expect($user->toArray())->toHaveKey('id');
    expect($user->toArray())->toHaveKey('age');
});
