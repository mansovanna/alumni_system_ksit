import {
  ComputerPhoneSyncIcon,
  DashboardSquare03Icon,
  EngineIcon,
  Mortarboard02Icon,
  PhysicsIcon,
  Settings01Icon,
  ToolsIcon,
  UserGroup02Icon,
  WheatIcon,
} from "@hugeicons/core-free-icons";
import { reactive } from "vue";

export const icons = reactive([
  { name: "dashboard", icon: DashboardSquare03Icon },
  { name: "users", icon: UserGroup02Icon },
  { name: "tools", icon: ToolsIcon },
  { name: "settings", icon: Settings01Icon },
  { name: "class", icon: Mortarboard02Icon },
  { name: "wheatIcon", icon: WheatIcon },
  { name: "engineIcon", icon: EngineIcon },
  { name: "physicsIcon", icon: PhysicsIcon },
  { name: "computer", icon: ComputerPhoneSyncIcon },
]);

export const colorGroups = reactive([
  {
    name: "primary",
    from: "#3b82f6",
    to: "#1d4ed8",
    text: "#1d4ed8",
  },
  {
    name: "secondary",
    from: "#64748b",
    to: "#334155",
    text: "#334155",
  },
  {
    name: "success",
    from: "#22c55e",
    to: "#15803d",
    text: "#15803d",
  },
  {
    name: "warning",
    from: "#f59e0b",
    to: "#b45309",
    text: "#b45309",
  },
  {
    name: "danger",
    from: "#ef4444",
    to: "#991b1b",
    text: "#991b1b",
  },
  {
    name: "info",
    from: "#3b82f6",
    to: "#1e40af",
    text: "#1e40af",
  },
  {
    name: "purple",
    from: "#a855f7",
    to: "#6b21a8",
    text: "#6b21a8",
  },
  {
    name: "pink",
    from: "#ec4899",
    to: "#9d174d",
    text: "#9d174d",
  },
  {
    name: "rose",
    from: "#f43f5e",
    to: "#9f1239",
    text: "#9f1239",
  },
  {
    name: "orange",
    from: "#f97316",
    to: "#9a3412",
    text: "#9a3412",
  },
  {
    name: "amber",
    from: "#fbbf24",
    to: "#92400e",
    text: "#92400e",
  },
  {
    name: "lime",
    from: "#84cc16",
    to: "#3f6212",
    text: "#3f6212",
  },
  {
    name: "emerald",
    from: "#10b981",
    to: "#065f46",
    text: "#065f46",
  },
  {
    name: "teal",
    from: "#14b8a6",
    to: "#115e59",
    text: "#115e59",
  },
  {
    name: "cyan",
    from: "#06b6d4",
    to: "#155e75",
    text: "#155e75",
  },
  {
    name: "sky",
    from: "#0ea5e9",
    to: "#075985",
    text: "#075985",
  },
  {
    name: "indigo",
    from: "#6366f1",
    to: "#3730a3",
    text: "#3730a3",
  },
  {
    name: "violet",
    from: "#8b5cf6",
    to: "#5b21b6",
    text: "#5b21b6",
  },
  {
    name: "fuchsia",
    from: "#d946ef",
    to: "#86198f",
    text: "#86198f",
  },
  {
    name: "slate",
    from: "#94a3b8",
    to: "#334155",
    text: "#334155",
  },
]);

export const workStatus = reactive([
  {
    id: 1,
    title: "មានការងារធ្វើ",
    status: "employed",
  },
  {
    id: 2,
    title: "កំពុងស្វែងរកការងារធ្វើ",
    status: "seeking",
  },
  {
    id: 3,
    title: "សិក្សារបន្ដ",
    status: "continuing_study",
  },
  {
    id: 4,
    title: "មិនទាន់បានការងារធ្វើ",
    status: "unemployed",
  },
]);

