<template>
  <header id="topbar">
    <div class="topbar-left">
      <button class="btn-icon" id="menu-toggle" @click="$emit('toggle-sidebar')">
        <i class="lucide" data-lucide="menu" style="width:16px;height:16px"></i>
      </button>
      <span class="topbar-title">{{ pageTitle }}</span>
    </div>
    <div class="topbar-right">
      <button class="btn-icon">
        <i class="lucide" data-lucide="bell" style="width:16px;height:16px"></i>
        <span class="notif-dot"></span>
      </button>
      <button class="btn-icon">
        <i class="lucide" data-lucide="search" style="width:16px;height:16px"></i>
      </button>
      <div class="avatar">AD</div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
const route = useRoute();
const pageTitles = {
  'dashboard':'Dashboard','master-kamar':'Tipe Kamar','master-customer':'Customer',
  'manajemen-kamar':'Manajemen Kamar','order':'Order / Penyewaan','pindah-kamar':'Pindah Kamar',
  'perbaikan':'Perbaikan','fasilitas':'Fasilitas Umum','log':'Log Aktivitas'
};
const pageTitle = computed(() => pageTitles[route.name] || route.name);
</script>

<style scoped>
@import '../style.css';
</style>
