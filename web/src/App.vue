<template>
  <header class="masthead">
    <div class="masthead__inner">
      <router-link class="brand" to="/"> Hotel</router-link>
      <nav class="nav">
        <router-link class="nav__link" to="/book">Book</router-link>
        <router-link class="nav__link" to="/dashboard">Dashboard</router-link>
        <template v-if="auth.user">
          <span class="nav__hello">Hi, {{ auth.user.name }}</span>
          <button class="btn btn--outline" @click="auth.logout()">Logout</button>
        </template>
        <template v-else>
          <router-link class="nav__link" to="/login">Login</router-link>
          <router-link class="btn" to="/register">Register</router-link>
        </template>
      </nav>
    </div>
  </header>
  <main class="container">
    <router-view />
  </main>
  <footer class="footer">© {{ new Date().getFullYear() }} Hotel</footer>
</template>

<script setup lang="ts">
import { useAuth } from './stores/auth';
const auth = useAuth();
</script>
