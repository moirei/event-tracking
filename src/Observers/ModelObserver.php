<?php

namespace MOIREI\EventTracking\Observers;

use Illuminate\Support\Arr;
use MOIREI\EventTracking\Contracts\TrackableModel;
use MOIREI\EventTracking\Facades\Events;
use MOIREI\EventTracking\Helpers;

class ModelObserver
{
    public function __construct(protected array $options = [])
    {
        //
    }

    public function created($user)
    {
        $eventName = $this->getEventName($user);
        Events::track($eventName, $this->getEventProperties($user, $eventName));
    }

    public function updated($user)
    {
        $eventName = $this->getEventName($user);
        Events::track($eventName, $this->getEventProperties($user, $eventName));
    }

    public function deleted($user)
    {
        $eventName = $this->getEventName($user);
        Events::track($eventName, $this->getEventProperties($user, $eventName));
    }

    public function restored($user)
    {
        $eventName = $this->getEventName($user);
        Events::track($eventName, $this->getEventProperties($user, $eventName));
    }

    protected function getEventName($model): string
    {
        $method = debug_backtrace()[1]['function'];
        $nameOption = Arr::get($this->options, $method);
        if (is_string(($nameOption))) {
            return $nameOption;
        }
        $nameOption = Arr::get($nameOption, 'name');

        $eventName = $nameOption ?? Helpers::resolveModelEvent($model, $method);

        if ($model instanceof TrackableModel) {
            return $model->getEventName($eventName);
        }

        return $eventName;
    }

    protected function getEventProperties($model, $eventName): array
    {
        if ($model instanceof TrackableModel) {
            return $model->getEventProperties($eventName);
        }

        $method = debug_backtrace()[1]['function'];
        $propertiesOption = Arr::get($this->options, $method);
        if (!is_string(($propertiesOption))) {
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
}
