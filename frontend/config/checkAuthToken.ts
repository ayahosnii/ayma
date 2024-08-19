export function checkAuthToken() {
  const token = localStorage.getItem('authToken');
  if (!token) {
    console.error('No auth token found');
    return false;
  }
  return token;
}
