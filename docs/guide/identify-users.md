# Identify users

As with most analytics libraries, you can identify and update a user profile

```php
// send only to mixpanel
Events::channel('mixpanel')->identify($user->id, [
    'createdAt'        => $user->createdAt,
    'updatedAt'        => $user->updatedAt,
    'name'             => $user->name,
    'firstName'        => $user->first_name,
    'firstName'        => $user->last_name,
    'email'            => $user->email,
    'phone'            => $user->phone,
    'city'             => $user->city,
    'region'           => $user->region,
    'country'          => $user->country,
    "Favorite Color"   => $user->favoriteColor,
]);
```

The `identify` utility does not currently support property remapping. The above example is for `Mixpanel`.

However, models that implement `EventUserProxy` or `EventUser` can directly provide core identification properties.

```php
...
use MOIREI\EventTracking\Contracts\EventUserProxy;

class User extends Model implements EventUserProxy{
    ...
}
```

```php
Events::identify($user);
```

## Active user

When `identify` is called, the provided user is cached as the active request user.

For the in-built `Mixpanel` channel, subsequent events are tracked against the user:

```php
Events::track('User Event');
```

You can also provide the active user with `user` without trigger an `identify` action.

```php
Events::user($user);
```

> User must be a `MOIREI\EventTracking\Objects\User` object or implements `MOIREI\EventTracking\Contracts\EventUserProxy` or `MOIREI\EventTracking\Contracts\EventUser` interface

### Example Middleware

The following middleware will automaticall set the authenticated user for all events during a request.

```php
namespace App\Http\Middleware;

use Closure;
use MOIREI\EventTracking\Contracts\EventUser as User;
use MOIREI\EventTracking\Contracts\EventUserProxy;
use MOIREI\EventTracking\Facades\Events;

class EventUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if ($user instanceof EventUserProxy || $user instanceof User) {
            Events::user($user);
        }

        return $next($request);
    }
}
```
