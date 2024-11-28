// graphql/queries/getProducts.js
import { gql } from '@apollo/client';

export const GET_PRODUCTS = gql`
    query GetProducts($limit: Int!) {
        products(limit: $limit) {
            id
            name
            price
        }
    }
`;
