export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()

  if (!authStore.isReady) {
    await authStore.fetchUser()
  }

  if (!authStore.isLoggedIn) {
    return navigateTo('/login')
  }

  if (!authStore.isAdmin) {
    return navigateTo('/admins')
  }
})