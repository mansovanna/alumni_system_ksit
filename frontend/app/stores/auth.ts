import type { UserModel } from "~/types/user.model";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null as UserModel | null,
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
    isAdmin: (state) => state.user?.role === "admin" || "staff",
  },

  actions: {
    async login(data: FormData) {
      const { $api } = useNuxtApp();
      this.isLoading = true;
      try {
        const response = await $api.post("/login", data);

        this.token = response.data.token;
        this.user = response.data.user;

        const tokenCookie = useCookie("auth_token", {
          maxAge: 60 * 60 * 24 * 7,
          path: "/",
          sameSite: "lax",
          secure: false,
        });
        tokenCookie.value = this.token;

        if (this.user?.role == "alumni") {
          return navigateTo("/");
        }
        // console.log(this.user?.role)
        return navigateTo("/admin");
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
      // console.log("🔑 Token from cookie:", token);

      if (!token) {
        this.clearAuth();
        this.isReady = true;
        return;
      }

      try {
        const { $api } = useNuxtApp();
        const response = await $api.get("/user");
        // console.log("✅ User fetched:", response.data);
        this.user = response.data?.data;
        this.token = token;
      } catch (e: any) {
        // console.log(
        //   "❌ Fetch user failed:",
        //   e.response?.status,
        //   e.response?.data,
        // ); 
        this.clearAuth();
      } finally {
        this.isReady = true;
      }
    },
    async logout() {
      const { $api } = useNuxtApp();
      try {
        await $api.post("/logout");
      } finally {
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
