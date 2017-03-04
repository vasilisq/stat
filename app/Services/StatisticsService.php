<?php

namespace App\Services;


use App\Entities\StatisticIndex;
use App\Entities\Visit;
use Illuminate\Support\Collection;

class StatisticsService
{
    /** @var Collection */
    protected $statistic;

    /**
     * StatisticsService constructor.
     * @param string[] $sections
     */
    public function __construct($sections)
    {
        $this->statistic = Collection::make();

        foreach ($sections as $sectionName) {
            $this->registerSection($sectionName);
        }
    }

    /**
     * Add fact to statistic
     *
     * @param Visit $visit
     */
    public function fact(Visit $visit)
    {
        $this->statistic->map(function (Collection $section, $sectionName) use ($visit) {
            // section name should be named as Visit property
            // here we retrieve getter's name, e.g. getBrowser
            $getter = 'get' . ucfirst($sectionName);

            if (!method_exists($visit, $getter)) {
                return $section;
            }

            // get visit property via getter
            $indexValue = $visit->$getter();

            // increment existing stats
            if ($section->has($indexValue)) {
                $section[$indexValue]->increment($visit);

                return $section;
            }

            // insert index
            $index = new StatisticIndex($indexValue);
            $index->increment($visit);
            $section->put($indexValue, $index);

            return $section;
        });

    }

    protected function registerSection($sectionName)
    {
        $this->statistic->put($sectionName, Collection::make());
    }

    /**
     * @return Collection
     */
    public function getStatistics()
    {
        return $this->statistic;
    }
}