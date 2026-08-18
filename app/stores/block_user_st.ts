export const useBlockUserSt = defineStore("user_st", {
  state: () => ({
    isUpdateStatus: false,
  }),

  actions: {
    handleUpdateStatus() {
      this.isUpdateStatus = !this.isUpdateStatus;
    },
  },
});
