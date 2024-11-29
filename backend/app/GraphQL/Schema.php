<?php

namespace App\GraphQL;

use GraphQL\Type\Schema as BaseSchema;

class Schema extends BaseSchema
{
    protected $mutation = 'App\GraphQL\Mutations\AddToCartMutation';
}
