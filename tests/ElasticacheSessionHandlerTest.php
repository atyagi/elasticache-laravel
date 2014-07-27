<?php
use Atyagi\Elasticache\ElasticacheSessionHandler;
use Mockery as m;

class ElasticacheSessionHandlerTest extends TestCase {

    /** @var ElasticacheSessionHandler */
    protected $sessionHandler;

    /** @var m\Mock */
    protected $mockMemcached;

    public function setUp()
    {
        parent::setUp();
        $this->mockMemcached = m::mock('Memcached');
        $mockConfig = m::mock('Config');

        $mockConfig->shouldReceive('get')
            ->with('session.lifetime')
            ->andReturn(10);

        $mockConfig->shouldReceive('get')
            ->with('session.cookie')
            ->andReturn('test_session_prefix');

        $this->mockApp->shouldReceive('make')
            ->with('config')
            ->andReturn($mockConfig);

        $this->sessionHandler =
            new ElasticacheSessionHandler($this->mockMemcached, $this->mockApp);
    }

    public function testSessionExpiryConvertedToSeconds()
    {
        $expiry = $this->sessionHandler->sessionExpiry;
        $this->assertEquals(600, $expiry);
    }

    public function testOpenReturnsTrueIfMemcachedExists()
    {
        $result = $this->sessionHandler->open('test_path', 'test_session');
        $this->assertTrue($result);
    }

    public function testOpenReturnsFalseIfMemcachedIsNull()
    {
        $handler = new ElasticacheSessionHandler(null, $this->mockApp);
        $result = $handler->open('test_path', 'test_session');
        $this->assertFalse($result);
    }

    public function testCloseReturnsTrue()
    {
        $result = $this->sessionHandler->close();
        $this->assertTrue($result);
    }

    public function testGcReturnsTrue()
    {
        $result = $this->sessionHandler->gc(m::anyOf('int'));
        $this->assertTrue($result);
    }

    public function testReadReturnsValueIfPresent()
    {
        $this->setGetExpectations('test_id', 'test_value');
        $value = $this->sessionHandler->read('test_id');
        $this->assertEquals('test_value', $value);
    }

    public function testReadReturnsEmptyStringIfNotPresent()
    {
        $this->setGetExpectations('test_id', false);
        $value = $this->sessionHandler->read('test_id');
        $this->assertEquals('', $value);
    }

    public function testWriteSessionCallsAddIfNotPresent()
    {
        $this->setGetExpectations('test_id', false);
        $this->mockMemcached->shouldReceive('add')
            ->once()->andReturn(true);
        $this->mockMemcached->shouldReceive('replace')
            ->never();

        $result = $this->sessionHandler->write('test_id', 'test_data');
        $this->assertTrue($result);
    }

    public function testWriteSessionCallsReplaceIfPresent()
    {
        $this->setGetExpectations('test_id', true);
        $this->mockMemcached->shouldReceive('replace')
            ->once()->andReturn(true);
        $this->mockMemcached->shouldReceive('add')
            ->never();

        $result = $this->sessionHandler->write('test_id', 'test_data');
        $this->assertTrue($result);
    }

    public function testDestroy()
    {
        $this->mockMemcached->shouldReceive('delete')
            ->once()->with('test_session_prefix_test_id')
            ->andReturn(true);

        $result = $this->sessionHandler->destroy('test_id');
        $this->assertTrue($result);
    }

    private function setGetExpectations($id, $value)
    {
        $this->mockMemcached->shouldReceive('get')
            ->once()
            ->with('test_session_prefix_' . $id)
            ->andReturn($value);
    }

} 