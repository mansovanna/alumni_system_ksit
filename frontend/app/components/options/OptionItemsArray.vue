<script setup>
import ArrowIcon from "~/components/icons/ArrowIcon.vue";

const isOpen = ref(false);
const triggerRef = ref(null); // the <section> that holds the button
const panelRef = ref(null); // the teleported dropdown panel

const props = defineProps({
  title: { type: String, default: "Select all" },
  items: {
    type: Array,
    required: true,
  },
  active: {
    type: String,
    default: null,
  },
  zIndex: {
    type: String,
    default: "z-20",
  },
});

const emit = defineEmits(["update:active"]);

const handle = (id, title) => {
  const payload = { id, title };
  emit("update:active", payload);
  isOpen.value = false;
};
/* -------------------------------------------------- */
/* Position the teleported panel under the trigger      */
/* -------------------------------------------------- */
const panelStyle = ref({ top: "0px", left: "0px", width: "0px" });

const updatePosition = () => {
  const el = triggerRef.value;
  if (!el) return;
  const rect = el.getBoundingClientRect();
  panelStyle.value = {
    top: `${rect.bottom + 4}px`,
    left: `${rect.left}px`,
    width: `${rect.width}px`,
  };
};

const open = async () => {
  updatePosition();
  isOpen.value = true;
  await nextTick();
  updatePosition();
};

const close = () => {
  isOpen.value = false;
};

const toggle = () => {
  if (isOpen.value) {
    close();
  } else {
    open();
  }
};

/* -------------------------------------------------- */
/* Click outside (must check both trigger + teleported panel) */
/* -------------------------------------------------- */
const handleClickOutside = (event) => {
  if (!isOpen.value) return;
  const target = event.target;
  const clickedTrigger = triggerRef.value?.contains(target);
  const clickedPanel = panelRef.value?.contains(target);
  if (!clickedTrigger && !clickedPanel) {
    close();
  }
};

/* -------------------------------------------------- */
/* Keep position in sync while open (scroll/resize)     */
/* -------------------------------------------------- */
onMounted(() => {
  document.addEventListener("click", handleClickOutside, true);
  window.addEventListener("scroll", updatePosition, true); // capture phase catches inner scroll containers
  window.addEventListener("resize", updatePosition);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside, true);
  window.removeEventListener("scroll", updatePosition, true);
  window.removeEventListener("resize", updatePosition);
});
</script>

<template>
  <section
    class="w-full relative text-nowrap capitalize font-Inter"
    ref="triggerRef"
  >
    <button
      type="button"
      @click="toggle"
      class="w-full h-10 flex justify-between items-center gap-2 border border-slate-300 rounded-md px-4 py-2 bg-white focus:ring-2 focus:ring-primary/30 text-slate-500 text-sm"
    >
      <Transition name="fade-text" mode="out-in">
        <span class="capitalize">{{
          props.active && props.active.length > 0 ? props.active : title
        }}</span>
      </Transition>
      <ArrowIcon class="rotate-90" />
    </button>

    <Teleport to="body">
      <Transition name="dropdown">
        <div
          v-if="isOpen"
          ref="panelRef"
          :class="zIndex"
          class="fixed bg-white border border-slate-300 overflow-clip rounded-md py-0.5 text-sm text-left shadow-lg shadow-slate-900/10 max-h-50 overflow-y-auto [scrollbar-width:thin] [scrollbar-color:theme(colors.gray.400)_transparent] [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-400 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb:hover]:bg-gray-500"
          :style="panelStyle"
        >
          <div v-if="items" class="w-full flex flex-col gap-0.5">
            <button
              type="button"
              @click="handle(0, null)"
              class="px-4 py-1.5 w-full capitalize hover:bg-slate-100 text-left font-kantumruy-pro text-base"
              :class="active == null ? 'bg-slate-200' : ''"
            >
              {{ title }}
            </button>
            <div v-for="(item, index) in items" :key="index">
              <button
                type="button"
                @click="handle(item.id, item.title)"
                class="px-4 py-1.5 capitalize w-full hover:bg-slate-100 text-left font-Inter"
                :class="active == item.title ? 'bg-slate-200' : ''"
              >
                {{ item.title }}
              </button>
            </div>
          </div>
          <div
            v-else
            class="w-full px-3 py-2 font-Inter text-slate-400 text-center"
          >
            No data
          </div>
        </div>
      </Transition>
    </Teleport>
  </section>
</template>

<style>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

.fade-text-enter-active,
.fade-text-leave-active {
  transition: all 0.2s ease;
}

.fade-text-enter-from,
.fade-text-leave-to {
  opacity: 0;
  transform: translateY(6px);
}
</style>
