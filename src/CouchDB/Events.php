<?php
namespace CouchDB;
/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
final class Events
{
    const preDropDatabase = 'preDropDatabase';
    const postDropDatabase = 'postDropDatabase';

    const preCreateDatabase = 'preCreateDatabase';
    const postCreateDatabase = 'postCreateDatabase';
}
