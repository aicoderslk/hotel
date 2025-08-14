<template>
    <section class="contact-page">
      <!-- Steps -->
      <div class="steps">
        <div class="step active">1 SEARCH</div>
        <div class="step active">2 SELECT ROOM</div>
        <div class="step active">3 CONTACT DETAILS</div>
        <div class="step">4 CONFIRMATION</div>
      </div>
  
      <div class="content">
        <!-- Left: Contact Form -->
        <div class="contact-form">
          <h3>CONTACT INFORMATION</h3>
          <label>
            Title
            <select v-model="title">
              <option>Mr.</option>
              <option>Mrs.</option>
              <option>Ms.</option>
            </select>
          </label>
          <label>
            Name
            <input type="text" v-model="name" />
          </label>
          <label>
            Email Address
            <input type="email" v-model="email" />
          </label>
          <button class="proceed" @click="proceed">PROCEED</button>
        </div>
  
        <!-- Right: Booking Summary -->
        <div class="summary">
          <div class="dates">{{ formatDate(checkIn) }} → {{ formatDate(checkOut) }}</div>
          <div class="nights">{{ nights }} NIGHT | {{ guests }} GUEST</div>
          <div class="image">340 x 210</div>
          <div class="room-title">{{ room?.name }}</div>
          <div class="price-row">
            <span>Room</span>
            <span>{{ formatCurrency(room?.price_cents / 100) }}</span>
          </div>
          <div class="price-row">
            <span>Tax & Service Charges (9%)</span>
            <span>{{ formatCurrency(tax) }}</span>
          </div>
          <div class="total">
            <span>TOTAL</span>
            <span>{{ formatCurrency(total) }}</span>
          </div>
        </div>
      </div>
    </section>
  </template>
  
  <script setup lang="ts">
  import { ref, computed, onMounted } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import { api } from '../api';
  
  const route = useRoute();
  const router = useRouter();
  
  const title = ref('Mr.');
  const name = ref('');
  const email = ref('');
  
  const checkIn = ref('');
  const checkOut = ref('');
  const guests = ref(1);
  const room = ref<any>(null);
  
  const nights = computed(() => {
    if (!checkIn.value || !checkOut.value) return 0;
    return Math.round((new Date(checkOut.value).getTime() - new Date(checkIn.value).getTime()) / (1000 * 60 * 60 * 24));
  });
  const tax = computed(() => (room.value ? (room.value.price_cents / 100) * 0.09 : 0));
  const total = computed(() => (room.value ? (room.value.price_cents / 100) + tax.value : 0));
  
  onMounted(async () => {
    checkIn.value = String(route.query.check_in || '');
    checkOut.value = String(route.query.check_out || '');
    guests.value = Number(route.query.guests || 1);
  
    if (route.query.room_id) {
      room.value = await api(`/rooms/${route.query.room_id}`);
    }
  });
  
  function formatDate(date: string) {
    return date ? new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).toUpperCase() : '';
  }
  function formatCurrency(val: number) {
    return 'S$' + val.toFixed(2);
  }
  
  function proceed() {
    router.push({
      path: '/confirmation',
      query: {
        room_id: route.query.room_id,
        check_in: checkIn.value,
        check_out: checkOut.value,
        guests: guests.value,
        name: name.value,
        email: email.value
      }
    });
  }
  </script>
  
  <style scoped>
  .steps { display: flex; gap: 8px; margin-bottom: 12px; }
  .step { padding: 4px 8px; background: #ddd; border-radius: 12px; }
  .step.active { background: black; color: white; }
  .content { display: flex; gap: 20px; }
  .contact-form, .summary { background: #f7f6f0; padding: 20px; flex: 1; }
  .image { background: #ccc; height: 150px; display: flex; align-items: center; justify-content: center; margin: 12px 0; }
  .price-row, .total { display: flex; justify-content: space-between; margin-top: 4px; }
  .total { margin-top: 12px; font-weight: bold; }
  .proceed { margin-top: 20px; background: black; color: white; padding: 8px 12px; border: none; cursor: pointer; }
  </style>
  