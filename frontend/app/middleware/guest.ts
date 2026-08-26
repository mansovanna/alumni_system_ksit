export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore();

  if (!authStore.isReady) {
    await authStore.fetchUser();
  }

  if (authStore.isLoggedIn) {
    if (authStore.user?.data.user.role.name == "alumni") {
      return navigateTo("/");
    } else {
      return navigateTo("/admins");
    }
  }
});
