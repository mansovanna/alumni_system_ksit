import axios from "axios";

export default defineNuxtPlugin((nuxtApp) => {
  const config = useRuntimeConfig();

  const api = axios.create({
    baseURL: config.public.apiBase,
    headers: { Accept: "application/json", "Content-Type": "application/json" },
  });

  api.interceptors.request.use((request) => {
    const token = useCookie("auth_token").value;
    if (token) {
      request.headers.Authorization = `Bearer ${token}`;
    }
    return request;
  });

  api.interceptors.response.use(
    (response) => response,
    (error) => {
      if (error.response?.status == 401) {
        nuxtApp.runWithContext(() => {
          const authStore = useAuthStore();
          authStore.clearAuth();
          navigateTo("/login");
        });
      }

      return Promise.reject(error);
    },
  );

  return { provide: { api } };
});
