<?php

namespace AsseticBundle\CacheBuster;

/**
 * Stub class for backwards compatibility.
 *
 * Since PHP 7 adds "null" as a reserved keyword, we can no longer have a class
 * named that and retain PHP 7 compatibility. The original class has been
 * renamed to "NoCache", and this class is now an extension of it. It raises an
 * E_USER_DEPRECATED to warn users to migrate.
 *
 * @deprecated
 */
class Null extends NoCache
{
    public function __construct($typeOrOptions = null)
    {
        trigger_error(
            sprintf(
                'The class %s has been deprecated; please use %s\\NoCache',
                __CLASS__,
                __NAMESPACE__
            ),
            E_USER_DEPRECATED
        );
    }
}
