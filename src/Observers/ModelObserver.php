<?php

namespace MOIREI\EventTracking\Observers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MOIREI\EventTracking\Contracts\TrackableModel;
use MOIREI\EventTracking\Facades\Events;
use MOIREI\EventTracking\Helpers;

abstract class ModelObserver
{
    protected array $options = [];

    protected static array $optionsCache = [];

    protected array $handle = ['created', 'updated',  'restored'];

    protected array $other = ['retrieved', 'creating', 'updating', 'saving', 'deleting', 'deleted', 'restoring', 'forceDeleted'];

    protected array $reserved = ['$all', '$only', '$except'];

    public function __construct()
    {
        $options = $this->options = static::getOptions();
        $extraEvents = array_filter(
            array_keys($options),
            function ($option) {
                return ! in_array($option, $this->reserved);
            }
        );
        if (count($extraEvents)) {
            $this->handle = array_merge($this->handle, $extraEvents);
        }

        if (Arr::get($options, '$all')) {
            $this->handle = array_merge($this->handle, $this->other);
        } elseif ($only = Arr::get($options, '$only')) {
            $this->handle = $only;
        } elseif ($except = Arr::get($options, '$except')) {
            $this->handle = array_filter(
                array_merge($this->handle, $this->other),
                function ($option) use ($except) {
                    return ! in_array($option, $except);
                }
            );
        }
    }

    /**
     * Create a new observer class.
     *
     * @param  array  $options
     * @return string
     */
    public static function make(array $options): string
    {
        // TODO: include event handler methods (e.g. "retrieved") dynamically.

        $className = 'EventTracking_'.Str::random().'_'.time().'_ModelObserver';
        $baseClass = ModelObserver::class;
        eval("class $className extends $baseClass{}");
        $className::setOptions($options);

        return $className;
    }

    /**
     * Create a new observer instance.
     *
     * @param  array  $options
     * @return ModelObserver
     */
    public static function factory(array $options): ModelObserver
    {
        $observer = static::make($options);

        return new $observer;
    }

    public function retrieved($model)
    {
        $this->handle('retrieved', $model);
    }

    public function creating($model)
    {
        $this->handle('creating', $model);
    }

    public function created($model)
    {
        $this->handle('created', $model);
    }

    public function updating($model)
    {
        $this->handle('updating', $model);
    }

    public function updated($model)
    {
        $this->handle('updated', $model);
    }

    public function saving($model)
    {
        $this->handle('saving', $model);
    }

    public function saved($model)
    {
        $this->handle('saved', $model);
    }

    public function deleting($model)
    {
        $this->handle('deleting', $model);
    }

    public function deleted($model)
    {
        $this->handle('deleted', $model);
    }

    public function restoring($model)
    {
        $this->handle('restoring', $model);
    }

    public function restored($model)
    {
        $this->handle('restored', $model);
    }

    public function forceDeleted($model)
    {
        $this->handle('forceDeleted', $model);
    }

    protected function getEventName($event, $model): string
    {
        $nameOption = Arr::get($this->options, $event);
        if (is_string(($nameOption))) {
            return $nameOption;
        }
        $nameOption = Arr::get($nameOption, 'name');

        $eventName = $nameOption ?? Helpers::resolveModelEvent($model, $event);

        if ($model instanceof TrackableModel) {
            return $model->getEventName($eventName);
        }

        return $eventName;
    }

    protected function getEventProperties($event, $model, $eventName): array
    {
        if ($model instanceof TrackableModel) {
            return $model->getEventProperties($eventName);
        }

        $propertiesOption = Arr::get($this->options, $event);
        if (! is_string(($propertiesOption))) {
            $propertiesOption = Arr::get($propertiesOption, 'properties');
            if (is_string($propertiesOption)) {
                if (method_exists($model, $propertiesOption)) {
                    return $model->$propertiesOption($eventName);
                } elseif (property_exists($model, $propertiesOption)) {
                    return $model->$propertiesOption;
                }
            } elseif (is_array($propertiesOption)) {
                return $propertiesOption;
            }
        }

        return [];
    }

    protected static function setOptions(array $options)
    {
        return Arr::set(static::$optionsCache, static::class, $options);
    }

    protected static function getOptions(): array
    {
        return Arr::get(static::$optionsCache, static::class, []);
    }

    protected function handle(string $event, $model)
    {
        $eventName = $this->getEventName($event, $model);
        if (in_array($event, $this->handle)) {
            Events::track($eventName, $this->getEventProperties($event, $model, $eventName));
        }
    }
}
