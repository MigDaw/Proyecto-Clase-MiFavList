import { ref } from 'vue';

export interface User {
  id: string;
  username: string;
  email: string;
  profilePic: string | null;
  perfilPublic: boolean;
  chatPublic: boolean;
}

export const userStore = ref<User | null>(null);
