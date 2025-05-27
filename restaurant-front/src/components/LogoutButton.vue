<template>
    <button @click="logout" class="logout-button">
      Dil (Logout)
    </button>
  </template>
  
  <script>
  export default {
    methods: {
      logout() {
        localStorage.removeItem('token');
        this.$router.push('/login');
      }
    }
  }
  </script>
  
  <style scoped>
  .logout-button {
    background-color: #ff4d4d;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  </style>
  