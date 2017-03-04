<?php

namespace App\Repositories;

use App\Entities\Visit;
use Illuminate\Support\Collection;

/**
 * Interface VisitRepositoryInterface
 *
 * I've made an repo interface, so we could change
 * repository storage when it's needed
 *
 * @package App\Repositories
 */
interface VisitRepositoryInterface
{
    /**
     * @param Visit $visit
     * @return mixed
     */
    public function save(Visit $visit);

    /**
     * Find all visits
     * @return $this
     */
    public function find();

    /**
     * Find visits according to uri
     * @param string $uri
     * @return $this
     */
    public function findWhereUri($uri);

    /**
     * Get list of URIs
     * @return Collection
     */
    public function getUriList();

    /**
     * Walk through results
     *
     * @param \Closure $closure
     * @return $this
     */
    public function walk(\Closure $closure);
}