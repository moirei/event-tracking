# Installation

```bash
composer require moirei/event-tracking
```

## Publish the config

```bash
php artisan vendor:publish --tag=event-tracking
```

This will create the following configuration file:

```bash
config/event-tracking.php
```

Use this file to configure event channels, auto-tracking behavior, user identification, and more.
