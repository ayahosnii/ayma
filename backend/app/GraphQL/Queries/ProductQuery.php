<?php

namespace App\GraphQL\Queries;

use App\Models\MongoModels\ProductsMongo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;


class ProductQuery extends Query
{
    protected $attributes = [
        'name' => 'products',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Product'));
    }

    public function args(): array
    {
        return [
            'category' => [
                'type' => Type::string(),
                'description' => 'The category to filter products by',
            ],
            'limit' => [
                'type' => Type::int(),
                'description' => 'Limit the number of products returned',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $query = ProductsMongo::query();

//        if (isset($args['category'])) {
//            $query->whereHas('category', function (Builder $q) use ($args) {
//                $q->where('name', $args['category']);
//            });
//        }
//
//        if (isset($args['limit'])) {
//            $query->limit($args['limit']);
//        }

        return $query->get();
    }
}
