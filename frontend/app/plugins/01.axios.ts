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


  

  return { provide: { api } };
});
