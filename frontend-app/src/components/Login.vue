<template>
  <div class="login">
    <h2>Login</h2>
    <form @submit.prevent="handleLogin" v-if="!isAuthenticated" class="login-form">
      <input v-model="username" type="text" placeholder="Username" class="input" />
      <input v-model="password" type="password" placeholder="Password" class="input" />
      <button type="submit" class="login-button">Login</button>
    </form>
    <p v-else class="authenticated-message">You are already logged in. Redirecting...</p>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

export default {
  setup() {
    const username = ref('')
    const password = ref('')
    const router = useRouter()
    const isAuthenticated = ref(false)

    // Check if token exists on mount
    onMounted(() => {
      const token = localStorage.getItem('token')
      if (token) {
        isAuthenticated.value = true
        // Redirect to home or another page
        router.push('/home')
      }
    })

    const handleLogin = async () => {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/login', {
          username: username.value,
          password: password.value,
        })

        // Store the JWT token in localStorage
        localStorage.setItem('token', response.data.token)

        // Redirect to Home
        router.push('/home')
      } catch (error) {
        console.error('Login failed:', error)
      }
    }

    return { username, password, handleLogin, isAuthenticated }
  },
}
</script>

<style scoped>
/* Global Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Arial', sans-serif;
  background-color: #f2f2f2;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

h2 {
  text-align: center;
  color: #333;
  margin-bottom: 20px;
}

/* Login Container */
.login {
  background-color: white;
  border-radius: 8px;
  padding: 20px 30px;
  max-width: 50%;
  width: 100%;
  margin-left: auto;
  margin-right: auto;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

/* Input Styling */
.input {
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  outline: none;
  transition: border-color 0.3s;
}

.input:focus {
  border-color: #007042;
}

/* Button Styling */
.login-button {
  padding: 12px;
  background-color: #007042;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
}


/* Message Styling */
.authenticated-message {
  text-align: center;
  color: green;
  font-size: 16px;
}

@media (max-width: 480px) {
 .login{
  max-width: 90%;
 }
}
</style>
