<template>
  <nav id="sidebar">
    <div class="sidebar-logo">
      <h1>Kos<span>Manager</span></h1>
      <p>v1.0.0 · admin@kosmanager.id</p>
    </div>
    <div class="sidebar-nav">
      <div class="nav-section">
        <div class="nav-label">Utama</div>
        <button class="nav-item" :class="{active: isActive('dashboard')}" @click="go('dashboard')">
          <i class="lucide" data-lucide="layout-dashboard"></i> Dashboard
        </button>
      </div>
      <div class="nav-section">
        <div class="nav-label">Master Data</div>
        <button class="nav-item" :class="{active: isActive('master-kamar')}" @click="go('master-kamar')">
          <i class="lucide" data-lucide="door-open"></i> Tipe Kamar
        </button>
        <button class="nav-item" :class="{active: isActive('master-customer')}" @click="go('master-customer')">
          <i class="lucide" data-lucide="users"></i> Customer
        </button>
      </div>
      <div class="nav-section">
        <div class="nav-label">Operasional</div>
        <button class="nav-item" :class="{active: isActive('manajemen-kamar')}" @click="go('manajemen-kamar')">
          <i class="lucide" data-lucide="grid-2x2"></i> Manajemen Kamar
        </button>
        <button class="nav-item" :class="{active: isActive('order')}" @click="go('order')">
          <i class="lucide" data-lucide="file-text"></i> Order / Sewa <span class="badge">3</span>
        </button>
        <button class="nav-item" :class="{active: isActive('pindah-kamar')}" @click="go('pindah-kamar')">
          <i class="lucide" data-lucide="arrow-right-left"></i> Pindah Kamar
        </button>
        <button class="nav-item" :class="{active: isActive('perbaikan')}" @click="go('perbaikan')">
          <i class="lucide" data-lucide="wrench"></i> Perbaikan <span class="badge">2</span>
        </button>
      </div>
      <div class="nav-section">
        <div class="nav-label">Lainnya</div>
        <button class="nav-item" :class="{active: isActive('fasilitas')}" @click="go('fasilitas')">
          <i class="lucide" data-lucide="building-2"></i> Fasilitas Umum
        </button>
        <button class="nav-item" :class="{active: isActive('log')}" @click="go('log')">
          <i class="lucide" data-lucide="scroll-text"></i> Log Aktivitas
        </button>
      </div>
    </div>
    <div class="sidebar-footer">
      <button class="nav-item" style="color:var(--slate-500)"><i class="lucide" data-lucide="settings"></i> Pengaturan</button>
      <button class="nav-item" style="color:#ef4444"><i class="lucide" data-lucide="log-out"></i> Keluar</button>
    </div>
  </nav>
</template>

<script setup>
import { useRouter, useRoute } from 'vue-router';
const router = useRouter();
const route = useRoute();
function go(name) { router.push({ name }); }
function isActive(name) { return route.name === name; }
</script>

<style scoped>
@import '../style.css';
</style>
