export default defineNuxtPlugin(() => {
  addRouteMiddleware('levels/list', () => {
    console.log('this global middleware was added in a plugin and will be run on every route change')
  }, { global: true })

  addRouteMiddleware('levels/add', () => {
    console.log('this named middleware was added in a plugin and would override any existing middleware of the same name')
  })
})
