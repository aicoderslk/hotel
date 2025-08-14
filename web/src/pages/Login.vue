<template>
  <div class="card" style="max-width:420px;margin:auto">
    <h2 style="margin-top:0">Login</h2>
    <form @submit.prevent="submit">
      <input class="input" placeholder="Email" v-model="email" type="email" />
      <div style="height:8px"></div>
      <input class="input" placeholder="Password" v-model="password" type="password" />
      <div style="height:12px"></div>
      <button class="btn">Login</button>
      <p v-if="err" class="badge badge--danger" style="margin-top:10px">{{ err }}</p>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useAuth } from '../stores/auth';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const err = ref('');
const auth = useAuth();
const router = useRouter();

async function submit(){
  err.value = '';
  try {
    await auth.login({ email: email.value, password: password.value });
    router.push('/dashboard');
  } catch(e:any){ err.value = e?.message || e?.error || 'Login failed'; }
}
</script>
