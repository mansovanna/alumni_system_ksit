export const useSideBarStore = defineStore("sidebar", {
  state: () => ({
    isSide: false,
  }),
  getters: {
    sidebar: (state) => state.isSide,
  },
});
