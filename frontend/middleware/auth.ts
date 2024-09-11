// middleware/auth.ts
export default defineNuxtRouteMiddleware((to) => {
  if (process.server) {
    // Skip on the server-side
    return;
  }

  // Perform the check on the client-side
  const token = localStorage.getItem('accessToken');
  const isAuthenticated = !!token;

  console.log('Authenticated:', isAuthenticated);

  // Redirect to login if not authenticated and trying to access protected route
  if (!isAuthenticated && !['/login', '/register'].includes(to.path)) {
    return navigateTo('/login');
  }
});
