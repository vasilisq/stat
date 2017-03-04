<?php

namespace App\Http\Controllers;

use App\Entities\Visit;
use App\Repositories\VisitRepositoryInterface;
use App\Services\StatisticsService;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    /** @var VisitRepositoryInterface */
    protected $visitRepository;

    public function __construct(VisitRepositoryInterface $visitRepository)
    {
        $this->visitRepository = $visitRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home', ['uris' => $this->visitRepository->getUriList()]);
    }

    /**
     * Total stats
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function total()
    {
        $this->visitRepository->find();

        return $this->renderStats();
    }

    /**
     * Single uri stat
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function uri(Request $request)
    {
        if (!$request->has('uri')) {
            return redirect('/admin');
        }

        $this->visitRepository->findWhereUri(
            $request->get('uri')
        );

        return $this->renderStats();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function renderStats()
    {
        // push sections into service
        $statisticsService = new StatisticsService([
            'browser',
            'geo',
            'os',
            'referer'
        ]);

        // count each visit fact to our stats
        $this->visitRepository->walk(function (Visit $fact) use ($statisticsService) {
            $statisticsService->fact($fact);
        });

        return view('stat', ['stat' => $statisticsService->getStatistics()]);
    }
}
