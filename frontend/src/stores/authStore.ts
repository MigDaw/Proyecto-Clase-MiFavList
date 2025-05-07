import { ref } from 'vue';

export const userStore = ref<{id: string; username: string; email: string} | null>(null);
