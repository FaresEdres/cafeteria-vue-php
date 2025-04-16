import { defineStore } from 'pinia';
import { postRequest } from '@/services/httpClient';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
    }),
    getters: {
        isLoggedIn: (state) => !!state.user,
    },
    actions: {
        async fetchUser() {
            try {
                const res = await postRequest('authenticated');
                if (res && res.role) {
                    this.user = res;
                } else {
                    this.user = null;
                }
            } catch (e) {
                this.user = null;
            }
        },
        async logout() {
            await postRequest('logout');
            this.user = null;
        }
    },
});
