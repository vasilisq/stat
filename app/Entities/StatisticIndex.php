<?php

namespace App\Entities;

use Illuminate\Support\Collection;

/**
 * Class StatisticIndex
 *
 * This class contains data about one single statistic index.
 * E.g.:
 *
 * Section 'Browsers' => Index 'Mozilla' => [100 hits, 20 unique ips, 20 unique cookies]
 *
 * @package App\Entities
 */
class StatisticIndex
{
    /**
     * Index value
     * @var string
     */
    protected $indexValue;

    /** @var int */
    protected $hits = 0;

    /** @var int */
    protected $uniqueIpHits = 0;

    /** @var int */
    protected $uniqueCookieHits = 0;

    /**
     * Ips which are already counted in this stat
     *
     * @var array
     */
    protected $ips;

    /**
     * Cookies which are already counted in this stat
     *
     * @var array
     */
    protected $cookies;


    /**
     * @return int
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * @param int $hits
     * @return StatisticIndex
     */
    public function setHits($hits)
    {
        $this->hits = $hits;

        return $this;
    }

    /**
     * @return int
     */
    public function getUniqueIpHits()
    {
        return $this->uniqueIpHits;
    }

    /**
     * @param int $uniqueIpHits
     * @return StatisticIndex
     */
    public function setUniqueIpHits($uniqueIpHits)
    {
        $this->uniqueIpHits = $uniqueIpHits;

        return $this;
    }

    /**
     * @return int
     */
    public function getUniqueCookieHits()
    {
        return $this->uniqueCookieHits;
    }

    /**
     * @param int $uniqueCookieHits
     * @return StatisticIndex
     */
    public function setUniqueCookieHits($uniqueCookieHits)
    {
        $this->uniqueCookieHits = $uniqueCookieHits;

        return $this;
    }

    /**
     * @return string
     */
    public function getIndexValue()
    {
        return $this->indexValue;
    }

    /**
     * StatisticIndex constructor.
     * @param string $indexValue
     */
    public function __construct($indexValue)
    {
        $this->indexValue = $indexValue;
        $this->ips = Collection::make();
        $this->cookies = Collection::make();
    }

    /**
     * Count a fact
     *
     * @param Visit $fact
     */
    public function increment(Visit $fact)
    {
        $this->hits++;

        // is this an unique ip visit?
        if (!$this->ips->contains($fact->getIp())) {
            $this->uniqueIpHits++;
            $this->ips->push($fact->getIp());
        }

        // is this an unique cookie visit?
        if (!$this->cookies->contains($fact->getCookieHash())) {
            $this->uniqueCookieHits++;
            $this->cookies->push($fact->getCookieHash());
        }
    }
}