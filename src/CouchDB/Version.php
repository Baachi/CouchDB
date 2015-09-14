<?php

namespace CouchDB;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
final class Version
{
    const VERSION = '1.0-beta';

    /**
     * Compare a given version with the current version.
     *
     * @param string $version
     *
     * @return int
     */
    public static function compare($version)
    {
        return version_compare(self::VERSION, $version);
    }
}
