<?php

namespace App\Repositories;

use App\Entities\Visit;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Collection;
use Predis\Collection\Iterator;

/**
 * Class VisitRedisRepository
 * @package App\Repositories
 */
class VisitRedisRepository implements VisitRepositoryInterface
{
    /** @var \Predis\Client $redis */
    protected $redis;

    /**
     * @var string
     */
    protected $currentPattern = null;

    /**
     * ID Sequence
     */
    const VISIT_ID = 'next_visit_id';

    /**
     * Uri storage set
     */
    const URI_SET = 'uri_set';

    /**
     * VisitRedisRepository constructor.
     * @param RedisManager $redisManager
     */
    public function __construct(RedisManager $redisManager)
    {
        $this->redis = $redisManager->connection()->client();
    }

    /**
     * @inheritdoc
     */
    public function save(Visit $visit)
    {
        $key = static::key($this->getNextId(), $visit->getUri());
        $this->registerUri($visit->getUri());

        return $this->redis->set($key, $visit->toJson());
    }

    /**
     * @inheritdoc
     */
    public function find()
    {
        $this->currentPattern = static::key('*', '*');

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function findWhereUri($uri)
    {
        $this->currentPattern = static::key('*', $uri);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function walk(\Closure $closure)
    {
        $this->walkOverPattern($this->currentPattern, $closure);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUriList()
    {
        return Collection::make(new Iterator\SetKey($this->redis, static::URI_SET))
            ->sort();
    }

    /**
     * Get URI from store
     *
     * @param string $uri
     */
    protected function registerUri($uri)
    {
        if ($this->redis->sismember(static::URI_SET, $uri) === 0) {
            $this->redis->sadd(static::URI_SET, $uri);
        }
    }

    /**
     * SCAN given pattern, and hande visit object
     *
     * @param string $pattern
     * @param \Closure $closure
     */
    protected function walkOverPattern($pattern, \Closure $closure)
    {
        foreach (new Iterator\Keyspace($this->redis, $pattern) as $key) {
            $json = $this->redis->get($key);
            $closure(Visit::fromJson($json));
        }
    }

    /**
     * @return int
     */
    protected function getNextId()
    {
        return $this->redis->incr(static::VISIT_ID);
    }

    /**
     * @return int
     */
    protected function getLastId()
    {
        return intval($this->redis->get(static::VISIT_ID));
    }

    /**
     * Get key pattern
     *
     * @param string $id
     * @param string $uri
     * @return string
     */
    protected static function key($id, $uri)
    {
        return 'visit:' . $id . ':' . $uri;
    }
}