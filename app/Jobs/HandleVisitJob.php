<?php

namespace App\Jobs;

use App\Entities\Visit;
use App\Repositories\VisitRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;

/**
 * Class HandleVisitJob
 *
 * This job registers visit
 *
 * @package App\Jobs
 */
class HandleVisitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Request Request, which we should analyze
     */
    protected $request;

    /**
     * HandleVisit constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var VisitRepositoryInterface $repository */
        $repository = app()->make(VisitRepositoryInterface::class);

        // register visit
        $repository->save(Visit::fromRequest($this->request));
    }
}
