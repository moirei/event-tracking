# Identify users

Just like most analytics libraries, this package allows you to identify users and update their profile information.

## Identify with properties

To identify a user and set profile properties (e.g. for **Mixpanel**), use:

```php
Events::channel('mixpanel')->identify($user->id, [
    'createdAt'       => $user->created_at,
    'updatedAt'       => $user->updated_at,
    'name'            => $user->name,
    'firstName'       => $user->first_name,
    'lastName'        => $user->last_name,
    'email'           => $user->email,
    'phone'           => $user->phone,
    'city'            => $user->city,
    'region'          => $user->region,
    'country'         => $user->country,
    'Favorite Color'  => $user->favoriteColor,
]);
```

> ⚠️ The `identify()` method does not currently support property remapping. The example above assumes Mixpanel formatting.

## Identify using a model

If your model implements `EventUserProxy` or `EventUser`, you can simplify identification:

```php
use MOIREI\EventTracking\Contracts\EventUserProxy;

class User extends Model implements EventUserProxy
{
    // Implement required methods
}
```

Then identify the user directly:

```php
Events::identify($user);
```

## Setting the active user

When `identify()` is called, the provided user is also stored as the active user for the request.

Any subsequent tracked events will automatically be associated with this user (for supported channels like Mixpanel):

```php
Events::track('User Event'); // Will be tied to the identified user
```

## Setting without identification

If you want to set the active user without triggering an identify call, use:

```php
Events::user($user);
```

This is useful when the user is already identified, or you want to associate them without updating their profile.

> The user must be an instance of:
>
> - MOIREI\EventTracking\Objects\User, or
> - A model implementing EventUser or EventUserProxy

## Middleware example: auto-set user

You can automatically set the user for all events during a request using middleware:

```php
namespace App\Http\Middleware;

use Closure;
use MOIREI\EventTracking\Facades\Events;
use MOIREI\EventTracking\Contracts\EventUser;
use MOIREI\EventTracking\Contracts\EventUserProxy;

class EventUserMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if ($user instanceof EventUser || $user instanceof EventUserProxy) {
            Events::user($user);
        }

        return $next($request);
    }
}
```

Add this middleware globally or to selected routes to ensure the authenticated user is automatically set.
