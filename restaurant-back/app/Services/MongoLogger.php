<?php

namespace App\Services;

use MongoDB\Client;

class MongoLogger
{
    protected $collection;

    public function __construct()
    {
        $client = new Client('mongodb://127.0.0.1:27017');
        $this->collection = $client->selectCollection('your_db', 'logs');
    }

    public function log($action, $userId, $data = [])
    {
        $this->collection->insertOne([
            'action' => $action,
            'user_id' => $userId,
            'data' => $data,
            'created_at' => now(),
        ]);
    }
}
