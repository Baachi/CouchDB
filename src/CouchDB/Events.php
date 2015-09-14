<?php

namespace CouchDB;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
final class Events
{
    const PRE_DROP_DATABASE = 'preDropDatabase';
    const POST_DROP_DATABASE = 'postDropDatabase';

    const PRE_CREATE_DATABASE = 'preCreateDatabase';
    const POST_CREATE_DATABASE = 'postCreateDatabase';
}
