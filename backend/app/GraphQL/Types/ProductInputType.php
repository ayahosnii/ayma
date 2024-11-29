<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class ProductInputType extends InputType
{
    // Set the attributes for the 'ProductInput' type, such as its name and description
    protected $attributes = [
        'name' => 'ProductInput', // The name of the input type (used in GraphQL queries)
        'description' => 'Input type for adding products to the cart with quantity', // A short description of the type's purpose
    ];

    /**
     * Define the fields of the 'ProductInput' input type
     *
     * This method returns an array of fields (attributes) that this input type will accept.
     * Each field represents a piece of data that is required when adding a product to the cart.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            // Define the 'product_id' field, which is required and should be a string
            'product_id' => [
                'type' => Type::nonNull(Type::string()), // Ensures that 'product_id' is required and must be a string
            ],

            // Define the 'quantity' field, which is required and should be an integer
            'quantity' => [
                'type' => Type::nonNull(Type::int()), // Ensures that 'quantity' is required and must be an integer
            ],
        ];
    }
}
