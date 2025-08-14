<template>
  <section class="select-room-page">
    <!-- Progress Steps -->
    <div class="steps">
      <div class="step active">
        <span class="circle">1</span>
        <span class="text">SEARCH</span>
      </div>
      <div class="step">
        <span class="circle">2</span>
        <span class="text">SELECT ROOM</span>
      </div>
      <div class="step">
        <span class="circle">3</span>
        <span class="text">CONTACT INFORMATION</span>
      </div>
      <div class="step">
        <span class="circle">4</span>
        <span class="text">CONFIRMATION</span>
      </div>
    </div>
    
    <!-- Dates & Guests -->
    <div class="search-bar text-black">
      <label>
        Check-in
        <input type="date" v-model="checkIn" />
      </label>
      <label>
        Check-out
        <input type="date" v-model="checkOut" />
      </label>
      <button @click="search">Search</button>
    </div>


    <div class="date-bar text-black">
      <div class="text-black">
        <strong>{{ formatDate(checkIn) }}</strong> → <strong>{{ formatDate(checkOut) }}</strong>
      </div>
      <div class="details">
        <div class="text-end">
          {{ nights }} NIGHT | 1 GUEST
        </div>
        <div class="sort">
          SORT BY:
          <select v-model="sortOrder">
            <option value="lowest">LOWEST PRICE</option>
            <option value="highest">HIGHEST PRICE</option>
          </select>
        </div>
      </div>

    </div>

    <!-- Search Filters -->

    <!-- Rooms -->
    <div class="room-list">
      <article v-for="r in sortedRooms" :key="r.id" class="room-card">
        <div class="room-description">
          <div class="room-img">340 x 210</div>
          <div class="room-info">
            <h3 class="room-title">{{ r.name }}</h3>
            <p class="subtitle">{{ r.type }}</p>
            <p class="desc">{{ r.description }}</p>
          </div>
        </div>
        <div class="room-price">
          <div class="price">S$ {{ (r.price_cents / 100).toLocaleString() }}<small class="desc">/night</small><br />
          </div>
          <small class="desc">Subject to GST and charges</small><br />
          <button class="book-btn" @click="book(r)">BOOK ROOM</button>
        </div>
      </article>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { api } from '../api';

const checkIn = ref('');
const checkOut = ref('');
const rooms = ref<any[]>([]);
const sortOrder = ref('lowest');

const nights = computed(() => {
  if (!checkIn.value || !checkOut.value) return 0;
  const inDate = new Date(checkIn.value);
  const outDate = new Date(checkOut.value);
  return Math.round((outDate.getTime() - inDate.getTime()) / (1000 * 60 * 60 * 24));
});

const sortedRooms = computed(() => {
  return [...rooms.value].sort((a, b) => {
    return sortOrder.value === 'lowest'
      ? a.price_cents - b.price_cents
      : b.price_cents - a.price_cents;
  });
});

async function search() {
  if (!checkIn.value || !checkOut.value) return;
  rooms.value = await api('/rooms');
}

function book(room: any) {
  window.location.href = `/book?room_id=${room.id}&check_in=${checkIn.value}&check_out=${checkOut.value}`;
}

function formatDate(date: string) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).toUpperCase();
}
</script>