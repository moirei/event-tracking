# Concepts

## Adapters

The media storage allows your application to save domain-specific files and folders in isolation. Storage disk, location, size, privacy, etc. can be managed at this level.

> Note that a model can make use of files from different storages.

A use case may be setting the active Storage in a middleware against an authenticated user.

## Event Channels

This package comes packed with multiple features including the ability to dynamically resize and filter uploaded images. This concept here is called _Imaging_. Handled by the `MagicImager` class, this package essentially provides dynamic imaging via urls manipulation similar to [cloudimage.io](https://www.cloudimage.io/) and other imaging servers.

For Vue lovers, [vue-imager](https://github.com/moirei/vue-imager) is a package that makes it very easy to display rich media served by this package.

## File identification

Files are identified by their UUID or Fully Qualified File Name (FQFN). A file's UUID is the prefered identification key while its FQFN is provided for client & route friendly urls.

The `find` method retrieves files by UUID. To retrieve by either UUID or FQFN, use the `get` method.

```php
$file = File::get(...);
```
