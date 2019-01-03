[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/maksimru/composite-primary-keys/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/maksimru/composite-primary-keys/?branch=master)
[![codecov](https://codecov.io/gh/maksimru/composite-primary-keys/branch/master/graph/badge.svg)](https://codecov.io/gh/maksimru/composite-primary-keys)
[![StyleCI](https://github.styleci.io/repos/163864737/shield?branch=master)](https://github.styleci.io/repos/163864737)
[![CircleCI](https://circleci.com/gh/maksimru/composite-primary-keys.svg?style=svg)](https://circleci.com/gh/maksimru/composite-primary-keys)

## About

Library extends Laravel's Eloquent ORM with pretty full support of composite keys

## Usage

No installation required

Simply add \MaksimM\CompositePrimaryKeys\Http\Traits\HasCompositePrimaryKey trait into required models

## Features Support

  
- increment and decrement
- update and save query
- binary columns
  
  Will convert binary column values into hex in json output
  
    ```php  
    class BinaryKeyUser extends Model
    {
        use \MaksimM\CompositePrimaryKeys\Http\Traits\HasCompositePrimaryKey;
    
        protected $binaryColumns = [
            'user_id'
        ];
    }
    ```
  
- model serialization in queues (with Illuminate\Queue\SerializesModels trait)

    Job:
    
    ```php
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
    ```
    
    Dispatch:
    
    ```php
    $model = TestUser::find([
        'user_id' => 1,
        'organization_id' => 100,
    ]);
    $this->dispatch(new TestJob($model));
    ```
    
- route implicit model binding support
  
    Model:
    
    ```php
    class TestBinaryUser extends Model
    {
        use \MaksimM\CompositePrimaryKeys\Http\Traits\HasCompositePrimaryKey;
        
        protected $table = 'binary_users';
        
        public $timestamps = false;
        
        protected $binaryColumns = [
          'user_id'
        ];
        
        protected $primaryKey = [
          'user_id',
          'organization_id',
        ];
    }
    ```
    
    routes.php:
    
    ```php
    $router->get('binary-users/{binaryUser}', function (BinaryUser $binaryUser) {
        return $binaryUser->toJson();
    })->middleware('bindings')
    ```
    
    request:
    
    ```http request
    GET /binary-users/D9798CDF31C02D86B8B81CC119D94836___100
    ```
    
    response:
    
    ```json
    {"user_id":"D9798CDF31C02D86B8B81CC119D94836","organization_id":"100","name":"Foo","user_id___organization_id":"D9798CDF31C02D86B8B81CC119D94836___100"}
    ```
