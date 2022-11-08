<?php

use MOIREI\EventTracking\Helpers;

uses()->group('helpers', 'helpers-isallin');

it('expect empty arrays to be true', function () {
    expect(Helpers::isAllIn([], []))->toBeTrue();
});

it('should return false for item not in haystack', function () {
    expect(Helpers::isAllIn(['z'], ['a', 'b']))->toBeFalse();
});

it('should return true for item in haystack', function () {
    expect(Helpers::isAllIn(['b'], ['a', 'b', 'c']))->toBeTrue();
});

it('should return false for mixed item in haystack', function () {
    expect(Helpers::isAllIn(['b', 'z'], ['a', 'b', 'c']))->toBeFalse();
});
