<template>
  <div>
    <h2>Your bookings</h2>

    <div class="card">
      <h3 style="margin:0 0 10px">Upcoming</h3>
      <table class="table " v-if="upcoming.length">
        <thead ><tr><th class="text-white">Room</th><th class="text-white">Dates</th><th class="text-white">Status</th><th></th></tr></thead>
        <tbody>
          <tr v-for="b in upcoming" :key="b.id">
            <td class="text-white">{{ b.room.name }}</td>
            <td class="text-white">{{ fmt(b.check_in_date) }} → {{ fmt(b.check_out_date) }}</td>
            <td><span class="badge badge--ok">{{ b.status }}</span></td>
          </tr>
        </tbody>
      </table>
      <div v-else class="badge">No upcoming bookings.</div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { api } from '../api';

const upcoming = ref<any[]>([]);
const past = ref<any[]>([]);

function fmt(d:string){ return new Date(d).toLocaleDateString(); }

async function load(){
  const data = await api('/bookings/me');
  upcoming.value = data.upcoming;
  past.value = data.past;
}
async function cancel(id:number){
  await api(`/bookings/${id}/cancel`, { method:'PATCH' });
  await load();
}
onMounted(load);
</script>
