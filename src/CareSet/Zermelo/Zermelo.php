<?php

namespace CareSet\Zermelo;

use CareSet\Zermelo\Models\ZermeloReport;
use ReflectionClass;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class Zermelo
{

    /**
     * The registered report names.
     *
     * @var array
     */
    public static $reports = [];


    /**
     * Register the given reports.
     *
     * @param  array  $resources
     * @return static
     */
    public static function reports(array $reports)
    {
        static::$reports = array_merge(static::$reports, $reports);

        return new static;
    }

    /**
     * Register all of the report classes in the given directory.
     *
     * @param  string  $directory
     * @return void
     */
    public static function reportsIn($directory)
    {
        $namespace = app()->getNamespace();

        $reports = [];

        foreach ((new Finder)->in($directory)->files() as $report) {
            $report = $namespace.str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($report->getPathname(), app_path().DIRECTORY_SEPARATOR)
                );

            if (is_subclass_of($report, ZermeloReport::class) &&
                ! (new ReflectionClass($report))->isAbstract()) {
                $reports[] = $report;
            }
        }

        static::reports(
            collect($reports)->sort()->all()
        );
    }

    /**
     * Get the report class name for a given key.
     *
     * @param  string  $key
     * @return string
     */
    public static function reportForKey($key)
    {
        return collect(static::$reports)->first(function ($value) use ($key) {
            return $value::uriKey() === $key;
        });
    }

}