import type { LoginResponseModel } from "~/types/LoginResponseModel";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null as LoginResponseModel | null,
    token: null as string | null,
    isReady: false,
    isLoading: false,
    errors: {
      login: "",
      password: "",
    },
  }),
  getters: {
    isLoggedIn: (state) => !!state.user,
    isAdmin: (state) =>
      state.user?.data?.user?.role.name === "admin" || "staff",
  },

  actions: {
    async login(data: FormData) {
      const { $api } = useNuxtApp();
      this.isLoading = true;
      try {
        const response = await $api.post("/login", data);

        this.user = response.data;
        this.token = this.user?.data.token ?? null;

        const tokenCookie = useCookie("auth_token", {
          maxAge: 60 * 60 * 24 * 7,
          path: "/",
          sameSite: "lax",
          secure: false,
        });
        tokenCookie.value = this.token;

        if (this.user?.data.user.role.name == "alumni") {
          return navigateTo("/");
        }

        return navigateTo("/admins");
      } catch (e: any) {
        this.errors.login = e.response.data.errors.login;
        this.errors.password = e.response.data.errors.password;
      } finally {
        this.isLoading = false;
      }
    },

    // register
    async registrer() {
      this.isLoading = true;
      let data = new FormData();
      data.append("login", "");
      data.append("password", "");

      try {
        //
      } catch (error) {
        console.log(error);
      } finally {
        this.isLoading = false;
      }
    },

    // Block get curren user
    async fetchUser() {
      const token = useCookie("auth_token").value;

      if (!token) {
        this.clearAuth();
        this.isReady = true;
        return;
      }
      this.isLoading = true;
      try {
        const { $api } = useNuxtApp();
        const response = await $api.get("/me");

        this.user = response.data;
        this.token = token;
      } catch (e: any) {
        this.clearAuth();
      } finally {
        this.isLoading = false;
        this.isReady = true;
      }
    },
    async logout() {
      const { $api } = useNuxtApp();
      this.isLoading = true;
      try {
        await $api.post("/logout");
      } finally {
        this.isLoading = false;
        this.clearAuth();
        navigateTo("/login");
      }
    },

    clearAuth() {
      this.user = null;
      this.token = null;
      useCookie("auth_token").value = null;
    },
  },
});
