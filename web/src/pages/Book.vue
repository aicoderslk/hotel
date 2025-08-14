<template>
  <section class="contact-page">
    <!-- Steps -->

    <div class="steps">
      <div class="step active">
        <span class="circle">1</span>
        <span class="text">SEARCH</span>
      </div>
      <div class="step active">
        <span class="circle">2</span>
        <span class="text">SELECT ROOM</span>
      </div>
      <div class="step active">
        <span class="circle">3</span>
        <span class="text">CONTACT INFORMATION</span>
      </div>
      <div class="step">
        <span class="circle">4</span>
        <span class="text">CONFIRMATION</span>
      </div>
    </div>

    <div class="content text-black">
      <!-- Left: Contact Form -->
      <div class="contact-form">
        <h3>CONTACT INFORMATION</h3>

        <label class="form-group">
          <span class="label-text">Title</span>
          <select v-model="title" class="form-input">
            <option>Mr.</option>
            <option>Mrs.</option>
            <option>Ms.</option>
          </select>
        </label>

        <label class="form-group">
          <span class="label-text">Name</span>
          <input type="text" v-model="name" class="form-input" />
        </label>

        <label class="form-group">
          <span class="label-text">Email Address</span>
          <input type="email" v-model="email" class="form-input" />
        </label>

        <div class="mt-2 text-green-600" v-if="msg">{{ msg }}</div>
        <div class="mt-2 text-red-500" v-if="err">{{ err }}</div>

        <button class="proceed" @click="submit">PROCEED</button>
      </div>

      <!-- Right: Booking Summary -->
      <div class="summary text-black" v-if="room">
        <div class="dates">{{ formatDate(checkIn) }} → {{ formatDate(checkOut) }}</div>
        <div class="nights">{{ nights }} NIGHT</div>
        <div class="nights"><strong>Room : {{ guests }} GUEST</strong></div>
        <div class="image">340 x 210</div>
        <div class="room-title">{{ room.name }}</div>
        <div class="price-row">
          <span>Room</span>
          <span>{{ formatCurrency(room.price_cents / 100) }}</span>
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

      <div v-else>
        <p class="text-red-500">Loading room details...</p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { api } from '../api';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

const title = ref('Mr.');
const name = ref('');
const email = ref('');
const checkIn = ref('');
const checkOut = ref('');
const guests = ref(1);
const room = ref<any>(null);

const msg = ref('');
const err = ref('');

const nights = computed(() => {
  if (!checkIn.value || !checkOut.value) return 0;
  const inDate = new Date(checkIn.value);
  const outDate = new Date(checkOut.value);
  return Math.round((outDate.getTime() - inDate.getTime()) / (1000 * 60 * 60 * 24));
});

const tax = computed(() => room.value ? (room.value.price_cents / 100) * 0.09 : 0);
const total = computed(() => room.value ? (room.value.price_cents / 100) + tax.value : 0);

onMounted(async () => {
  const roomId = route.query.room_id as string;
  checkIn.value = route.query.check_in as string || '';
  checkOut.value = route.query.check_out as string || '';
  guests.value = parseInt(route.query.guests as string) || 1;

  if (!roomId) {
    router.push('/');
    return;
  }

  try {
    const res = await api(`/rooms/${roomId}`);
    room.value = res;
  } catch (e) {
    console.error('Failed to load room', e);
  }
});

function formatDate(date: string) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short', day: 'numeric', year: 'numeric'
  }).toUpperCase();
}

function formatCurrency(val: number) {
  if (isNaN(val)) return 'S$0.00';
  return 'S$' + val.toFixed(2);
}

async function submit() {
  msg.value = '';
  err.value = '';

  if (!name.value || !email.value || !checkIn.value || !checkOut.value || !room.value) {
    err.value = 'Missing required fields';
    return;
  }

  try {
    const avail = await api(`/rooms/${room.value.id}/availability?check_in=${checkIn.value}&check_out=${checkOut.value}`);
    if (!avail.available) {
      err.value = 'Room not available';
      return;
    }

    const result = await api('/bookings', {
      method: 'POST',
      body: JSON.stringify({
        room_id: room.value.id,
        check_in_date: checkIn.value,
        check_out_date: checkOut.value,
        guests: guests.value,
        name: name.value,
        email: email.value,
        title: title.value
      })
    });

    msg.value = 'Booking confirmed!';

    router.push({
      name: 'confirmation',
      params: { id: result?.id }
    });

  } catch (e: any) {
    err.value = e?.message || e?.error || 'Failed';
  }
}
</script>
