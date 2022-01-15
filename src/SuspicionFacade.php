<?php
namespace Livingstoneco\Suspicion;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Livingstoneco\Suspicion\Skeleton\SkeletonClass
 */
class SuspicionFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'suspicion';
    }
}
