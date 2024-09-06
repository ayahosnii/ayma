// src/apollo.js
import { ApolloClient, createHttpLink, InMemoryCache } from '@apollo/client/core';

// HTTP connection to the API
const httpLink = createHttpLink({
  uri: 'http://localhost:8000/graphql',
});

// Cache implementation
const cache = new InMemoryCache();

// Create the Apollo Client
const apolloClient = new ApolloClient({
  link: httpLink,
  cache,
});

export default apolloClient;
