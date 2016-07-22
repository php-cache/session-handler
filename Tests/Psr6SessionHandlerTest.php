<?php

/*
 * This file is part of php-cache organization.
 *
 * (c) 2015-2015 Aaron Scherer <aequasi@gmail.com>, Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cache\SessionHandler\Tests;

use Cache\SessionHandler\Psr6SessionHandler;
use PHPUnit_Framework_MockObject_MockObject as mock;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Psr6SessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type Psr6SessionHandler
     */
    private $handler;

    /**
     * @type mock|CacheItemPoolInterface
     */
    private $mock;

    /**
     * @type mock|CacheItemInterface
     */
    private $itemMock;

    protected function setUp()
    {
        parent::setUp();

        $this->mock    = $this->getMockBuilder(CacheItemPoolInterface::class)->getMock();
        $this->handler = new Psr6SessionHandler($this->mock);

        $this->itemMock = $this->getMockBuilder(CacheItemInterface::class)->getMock();
        $this->mock->expects($this->any())
                   ->method('getItem')
                   ->with('psr6ses_sessionID')
                   ->will($this->returnValue($this->itemMock));
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(Psr6SessionHandler::class, $this->handler);
    }

    public function testOpen()
    {
        $this->assertTrue($this->handler->open('foo', 'bar'));
    }

    public function testClose()
    {
        $this->assertTrue($this->handler->close());
    }

    public function testGc()
    {
        $this->assertTrue($this->handler->gc(4711));
    }

    public function testRead()
    {
    }

    public function testWrite()
    {
        $handler = $this->handler;

        $item = $this->itemMock;
        $item->expects($this->once())
            ->method('expiresAfter')
            ->with(86400);

        $item->expects($this->once())
             ->method('set')
             ->with(['data']);

        $this->mock->expects($this->once())
                   ->method('save')
                   ->with($item);

        $handler->write('sessionID', ['data']);
    }

    public function testDestroy()
    {
    }

    public function testSetTtl()
    {
        $handler = $this->handler;
        $handler->setTtl(3);

        $item = $this->itemMock;
        $item->expects($this->once())
              ->method('expiresAfter')
              ->with(3);

        $handler->write('sessionID', 'data');
    }
}
