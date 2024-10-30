<?php

namespace App\Models\MongoModels;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Category extends Eloquent
{
    // Specify the MongoDB collection if different from default 'products_mongos'
    protected $collection = 'categories'; // or whatever your collection is called

    // Optionally, you can specify the connection for MongoDB
    protected $connection = 'mongodb';

}
