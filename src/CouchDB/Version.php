<?php
namespace CouchDB;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
final class Version
{
    const VERSION = '0.8';

    /**
     * Compare a given version with the current version
     *
     * @param  string  $version
     * @return integer
     */
    public static function compare($version)
    {
        return version_compare(self::VERSION, $version);
    }
}
