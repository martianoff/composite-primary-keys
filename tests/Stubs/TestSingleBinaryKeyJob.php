<?php

namespace MaksimM\CompositePrimaryKeys\Tests\Stubs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class TestSingleBinaryKeyJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $model;

    /**
     * Create a new job instance.
     */
    public function __construct(TestBinaryRoleHex $binaryRole)
    {
        $this->model = $binaryRole;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->model->update(['name' => 'Bar']);
    }
}
