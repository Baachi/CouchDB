<?php

namespace CouchDB\Tests\Util;

use CouchDB\Tests\TestCase;
use CouchDB\Util\BatchUpdater;
use GuzzleHttp\Psr7\Response;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class BatchUpdaterTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->database = $this->getMockBuilder('CouchDB\Database')
            ->disableOriginalConstructor()
            ->getMock();

        $this->database->expects($this->any())
            ->method('getName')
            ->willReturn('test');

        $this->updater = new BatchUpdater($this->client, $this->database);
    }

    public function testUpdate()
    {
        $this->updater->update(['_id' => 'bar1']);
        $this->updater->update(['_id' => 'bar2']);

        $this->mock->append(new Response(200, [], '{}'));

        $this->updater->execute();

        $request = $this->mock->getLastRequest();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/test/_bulk_docs', $request->getUri()->getPath());
        $this->assertEquals('{"docs":[{"_id":"bar1"},{"_id":"bar2"}]}', (string) $request->getBody());
    }

    public function testDelete()
    {
        $this->updater->delete('bar1', 'rev');
        $this->updater->delete('bar2', 'rev');

        $this->mock->append(new Response(200, [], '{}'));

        $this->updater->execute();

        $request = $this->mock->getLastRequest();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/test/_bulk_docs', $request->getUri()->getPath());
        $this->assertEquals('{"docs":[{"_id":"bar1","_rev":"rev","_deleted":true},{"_id":"bar2","_rev":"rev","_deleted":true}]}', (string) $request->getBody());
    }
}
