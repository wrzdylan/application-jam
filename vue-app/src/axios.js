import axios from 'axios';
import router from '@/router';

const instance = axios.create();

instance.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    const expiration = new Date(localStorage.getItem('tokenExpiration'));
    const now = new Date();
    
    if (token && expiration && now > expiration) {
        // Le token a expiré
        localStorage.removeItem('token');
        localStorage.removeItem('tokenExpiration');
        router.push('/login');  // Redirige vers la page de login
        return Promise.reject('Token expired');
    }
    
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, error => {
    return Promise.reject(error);
});

export default instance;