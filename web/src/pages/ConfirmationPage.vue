<template>
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
        <div class="step active">
            <span class="circle">4</span>
            <span class="text">CONFIRMATION</span>
        </div>
    </div>
    <section class="confirmation-page text-black text-center py-8" v-if="booking">
        <h1 class="text-3xl font-bold text-green-600">Booking Confirmed!</h1>
        <p class="mt-2">Thank you, {{ booking.name }}. Your reservation is secured.</p>

        <div class="summary-card mt-6 p-4 border rounded-md max-w-md mx-auto">
            <h2 class="text-lg font-semibold">{{ booking.room.name }}</h2>
            <p>{{ formatDate(booking.check_in_date) }} → {{ formatDate(booking.check_out_date) }}</p>
            <p>{{ booking.guests }} Guest(s)</p>
            <p class="font-bold mt-2">{{ formatCurrency(total) }}</p>
        </div>

        <router-link to="/dashboard" class="mt-6 inline-block bg-blue-600 text-white px-4 py-2 rounded">
            View My Bookings
        </router-link>
    </section>

    <p v-else class="text-red-500 text-center mt-8">Loading booking details...</p>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const booking = ref<any>(null);

// Fetch booking by ID from API
onMounted(async () => {
    const bookingId = route.params.id || route.query.id;
    if (!bookingId) return;

    try {
        const res = await fetch(`http://hotel.test/api/bookings/${bookingId}`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}` // replace with your auth method
            }
        });
        booking.value = await res.json();
    } catch (err) {
        console.error('Failed to fetch booking:', err);
    }
});

const total = computed(() => {
    if (!booking.value) return 0;
    return (booking.value.room.price_cents / 100) * 1.09;
});

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric'
    }).toUpperCase();
}

function formatCurrency(val: number) {
    return 'S$' + val.toFixed(2);
}
</script>