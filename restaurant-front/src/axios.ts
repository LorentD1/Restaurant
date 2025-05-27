import axios, { AxiosRequestConfig, InternalAxiosRequestConfig } from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  withCredentials: true
});

api.interceptors.request.use((config: InternalAxiosRequestConfig) => { // Change to InternalAxiosRequestConfig
  const token = localStorage.getItem('token');
  if (token && config.headers) { // Ensure that headers are defined before accessing them
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Example of using the api
api.get('/some-endpoint')
  .then(response => {
    console.log(response.data);
  })
  .catch(error => {
    console.error(error);
  });

export default api;
