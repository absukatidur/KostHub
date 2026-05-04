<template>
  <div style="display:flex;min-height:100vh">
    <Sidebar />
    <div id="main" style="flex:1;display:flex;flex-direction:column;min-height:100vh">
      <Topbar />
      <main id="page-content" style="flex:1;padding:24px;overflow-y:auto">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import Sidebar from './components/Sidebar.vue';
import Topbar from './components/Topbar.vue';
</script>
