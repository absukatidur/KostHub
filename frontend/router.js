import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './pages/Dashboard.vue';
// Placeholder components for other pages
const MasterKamar = { template: '<div>Tipe Kamar (migrate content here)</div>' };
const MasterCustomer = { template: '<div>Customer (migrate content here)</div>' };
const ManajemenKamar = { template: '<div>Manajemen Kamar (migrate content here)</div>' };
const Order = { template: '<div>Order / Penyewaan (migrate content here)</div>' };
const PindahKamar = { template: '<div>Pindah Kamar (migrate content here)</div>' };
const Perbaikan = { template: '<div>Perbaikan (migrate content here)</div>' };
const Fasilitas = { template: '<div>Fasilitas Umum (migrate content here)</div>' };
const Log = { template: '<div>Log Aktivitas (migrate content here)</div>' };

const routes = [
  { path: '/', name: 'dashboard', component: Dashboard },
  { path: '/master-kamar', name: 'master-kamar', component: MasterKamar },
  { path: '/master-customer', name: 'master-customer', component: MasterCustomer },
  { path: '/manajemen-kamar', name: 'manajemen-kamar', component: ManajemenKamar },
  { path: '/order', name: 'order', component: Order },
  { path: '/pindah-kamar', name: 'pindah-kamar', component: PindahKamar },
  { path: '/perbaikan', name: 'perbaikan', component: Perbaikan },
  { path: '/fasilitas', name: 'fasilitas', component: Fasilitas },
  { path: '/log', name: 'log', component: Log },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
