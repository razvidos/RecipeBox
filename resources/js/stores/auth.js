import {defineStore} from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        isLoggedIn: false,
    }),
    actions: {
        async login(email, password) {
            try {
                await axios.post('/login', {
                    email,
                    password,
                })
                await axios.get('/api/user')
                    .then(response => {
                        this.user = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
                this.isLoggedIn = true
            } catch (error) {
                console.error(error)
                throw new Error('Invalid login credentials')
            }
        },
        async register(name, email, password, password_confirmation) {
            try {
                const response = await axios.post('/register', {
                    name,
                    email,
                    password,
                    password_confirmation,
                })
                this.isLoggedIn = true
                this.user = response.data.user
                this.token = response.data.token
            } catch (error) {
                console.error(error)
                throw new Error('Invalid registration data')
            }
        },
        async logout() {
            await axios.post('/logout')
            this.isLoggedIn = false;
        },
    },
    getters: {
        user_id() {
            return this.user ? this.user.id : null;
        }
    }
})
