<?php

namespace MaksimM\CompositePrimaryKeys\Tests\Stubs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class TestJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $model;

    /**
     * Create a new job instance.
     */
    public function __construct(TestUser $testUser)
    {
        $this->model = $testUser;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->model->update(['counter' => 3333]);
    }
}
