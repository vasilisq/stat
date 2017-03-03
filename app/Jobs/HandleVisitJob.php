<?php

namespace App\Jobs;

use App\Entities\Visit;
use Redis;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;

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
        $uri = $this->request->getUri();

        $uniqueIpGlobal = Redis::command('SISMEMBER', ['ips', $this->request->ip()]) === 1;
        $uniqueCookiesGlobal = Redis::command('SISMEMBER', [
                'cookies',
                md5(json_encode($this->request->cookies->all()))
            ]) === 1;

        $uniqueIp = Redis::command('SISMEMBER', ['ips:' . $uri, $this->request->ip()]) === 1;
        $uniqueCookies = Redis::command('SISMEMBER', [
                'cookies:' . $uri,
                md5(json_encode($this->request->cookies->all()))
            ]) === 1;


        $visitId = Redis::command('INCR', ['next_visit_id']);

        $visit = new Visit($this->request);

        Redis::command('SET', [
            'visit:1',
            $visit->toJson()
        ]);


        //$this->request->ip();

        //Redis::command('SADD', ['ips']);
    }
}
