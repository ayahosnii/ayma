// composables/useGraphQL.js
import { useQuery } from '@apollo/client';

export function useGraphQL(query, variables = {}) {
  const { result, loading, error } = useQuery(query, { variables });

  return { data: result, loading, error };
}
