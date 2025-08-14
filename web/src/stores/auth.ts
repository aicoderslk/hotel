import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuth = defineStore('auth', {
  state: () => ({
    token: '',
    user: null as any
  }),
  actions: {
    async register(data: { name: string, email: string, password: string }) {
      const res = await axios.post('http://hotel.test/api/register', data, {
        withCredentials: true
      });
      this.token = res.data.token;
      this.user = res.data.user;

      // Save token for future API calls
      localStorage.setItem('token', this.token);
    },
    async login(data: { email: string, password: string }) {
      const res = await axios.post('http://hotel.test/api/login', data, {
        withCredentials: true
      });
      this.token = res.data.token;
      this.user = res.data.user;
      localStorage.setItem('token', this.token);
    }
  }
});
