export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore();

  if (!authStore.isReady) {
    await authStore.fetchUser();
  }

  if (authStore.isLoggedIn) {
    if (authStore.user?.role == "alumni") {
      return navigateTo("/");
    } else {
      return navigateTo("/admins");
    }
  }
});
