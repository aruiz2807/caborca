<script setup>
import UserLayout from "@Layouts/UserLayout.vue"
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card'
import { ChartContainer } from "@/Components/ui/chart";
import { VisDonut } from "@unovis/vue"
import { Donut } from "@unovis/ts"
import { computed, ref } from 'vue'
import axios from 'axios'

const props = defineProps({
  totalOrders: {
    type: Number,
    default: 0
  },
  attendedOrders: {
    type: Number,
    default: 0
  },
  pendingOrders: {
    type: Number,
    default: 0
  },
})

const currentYear = new Date().getFullYear()

// Interactive Table State
const activeFilter = ref(null) // 'all' | 'attended' | 'pending' | null
const selectedTitle = ref("")
const orders = ref([])
const isLoading = ref(false)

// Pie chart data
const data = computed(() => [
  { label: "Orders", value: props.totalOrders || 0.1 }, // Avoid 0 for chart rendering
])

// Pie chart data
const data_attended = computed(() => [
  { label: "Attended", value: props.attendedOrders },
  { label: "Orders", value: (props.totalOrders - props.attendedOrders) || 0 },
])

// Pie chart data
const data_pending = computed(() => [
  { label: "Pending", value: props.pendingOrders },
  { label: "Orders", value: (props.totalOrders - props.pendingOrders) || 0 },
])

// Define chart colors
const colors = {
  Orders: "hsl(216.3158 76% 49.0196%)",
  Attended: "hsl(221.3793 100% 94.3137%)",
  Pending: "hsl(221.3793 100% 94.3137%)",
}

// Fetch records dynamically
const fetchRecords = async (filterType, title) => {
  // If clicking the same category that is currently active, close it
  if (activeFilter.value === filterType && !isLoading.value) {
    closeTable()
    return
  }

  activeFilter.value = filterType
  selectedTitle.value = title
  orders.value = []
  isLoading.value = true

  try {
    const response = await axios.get(route('orders.dashboard_records'), {
      params: { filter: filterType }
    })
    orders.value = response.data.orders
  } catch (error) {
    console.error("Error fetching dashboard records:", error)
  } finally {
    isLoading.value = false
  }
}

const closeTable = () => {
  activeFilter.value = null
  selectedTitle.value = ""
  orders.value = []
}

// Map chart segment clicks via Unovis
const totalEvents = {
  [Donut.selectors.segment]: {
    click: () => fetchRecords('all', "Órdenes Solicitadas")
  }
}

const attendedEvents = {
  [Donut.selectors.segment]: {
    click: () => fetchRecords('attended', "Órdenes Atendidas (Finalizadas)")
  }
}

const pendingEvents = {
  [Donut.selectors.segment]: {
    click: () => fetchRecords('pending', "Órdenes Pendientes")
  }
}

// Helpers for the table
const getStatusLabel = (status) => {
  switch (status) {
    case 1: return 'Solicitado'
    case 2: return 'Revisión Refacciones'
    case 3: return 'Refacciones Disponibles'
    case 4: return 'Agendado'
    case 5: return 'Ingresado'
    case 6: return 'Finalizado'
    case 7: return 'No se presentó'
    default: return 'N/D'
  }
}

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 6: return 'bg-green-50 text-green-700 ring-green-600/20 ring-1'
    case 1: return 'bg-blue-50 text-blue-700 ring-blue-600/20 ring-1'
    case 4: return 'bg-purple-50 text-purple-700 ring-purple-600/20 ring-1'
    case 5: return 'bg-yellow-50 text-yellow-800 ring-yellow-600/20 ring-1'
    default: return 'bg-gray-50 text-gray-600 ring-gray-500/10 ring-1'
  }
}

const getIndicatorColor = (filter) => {
  switch (filter) {
    case 'attended': return 'bg-green-500'
    case 'pending': return 'bg-amber-500'
    default: return 'bg-blue-600'
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/D'
  try {
    const parts = dateString.split('-')
    if (parts.length === 3) {
      return `${parts[2]}/${parts[1]}/${parts[0]}`
    }
    return dateString
  } catch (e) {
    return dateString
  }
}
</script>

<template>
    <UserLayout tabTitle="Home" appName="Application">

        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                
                <!-- Card 1: Total Orders -->
                <div 
                    @click="fetchRecords('all', 'Órdenes Solicitadas')"
                    class="bg-muted/50 rounded-xl cursor-pointer hover:shadow-md transition duration-200 select-none group"
                >
                    <Card class="flex flex-col h-full border-2 border-transparent group-hover:border-primary/20">
                        <CardHeader class="items-center pb-0">
                            <CardTitle class="group-hover:text-primary transition-colors text-center">Indicador de ordenes solicitadas</CardTitle>
                            <CardDescription>Ordenes de {{ currentYear }}</CardDescription>
                        </CardHeader>

                        <CardContent>
                            <ChartContainer>
                                <!-- Donut chart component -->
                                <VisDonut
                                    :data="data"
                                    :value="d => d.value"
                                    :label="d => d.label"
                                    :color="d => colors[d.label]"
                                    :arc-width="25"
                                    :radius="85"
                                    :central-label-offset-y="5"
                                    :central-label="props.totalOrders.toString()"
                                    central-sub-label="Ordenes"
                                    :events="totalEvents"
                                />
                            </ChartContainer>
                        </CardContent>
                    </Card>
                </div>

                <!-- Card 2: Attended Orders -->
                <div 
                    @click="fetchRecords('attended', 'Órdenes Atendidas (Finalizadas)')"
                    class="bg-muted/50 rounded-xl cursor-pointer hover:shadow-md transition duration-200 select-none group"
                >
                    <Card class="flex flex-col h-full border-2 border-transparent group-hover:border-primary/20">
                        <CardHeader class="items-center pb-0">
                            <CardTitle class="group-hover:text-primary transition-colors text-center">Indicador de ordenes atendidas</CardTitle>
                            <CardDescription>Ordenes de {{ currentYear }}</CardDescription>
                        </CardHeader>

                        <CardContent>
                            <ChartContainer>
                                <!-- Donut chart component -->
                                <VisDonut
                                    :data="data_attended"
                                    :value="d => d.value"
                                    :label="d => d.label"
                                    :color="d => colors[d.label]"
                                    :arc-width="25"
                                    :radius="85"
                                    :central-label-offset-y="5"
                                    :central-label="props.attendedOrders.toString()"
                                    central-sub-label="Ordenes"
                                    :events="attendedEvents"
                                />
                            </ChartContainer>
                        </CardContent>
                    </Card>
                </div>

                <!-- Card 3: Pending Orders -->
                <div 
                    @click="fetchRecords('pending', 'Órdenes Pendientes')"
                    class="bg-muted/50 rounded-xl cursor-pointer hover:shadow-md transition duration-200 select-none group"
                >
                    <Card class="flex flex-col h-full border-2 border-transparent group-hover:border-primary/20">
                        <CardHeader class="items-center pb-0">
                            <CardTitle class="group-hover:text-primary transition-colors text-center">Indicador de ordenes pendientes</CardTitle>
                            <CardDescription>Ordenes de {{ currentYear }}</CardDescription>
                        </CardHeader>

                        <CardContent>
                            <ChartContainer>
                                <!-- Donut chart component -->
                                <VisDonut
                                    :data="data_pending"
                                    :value="d => d.value"
                                    :label="d => d.label"
                                    :color="d => colors[d.label]"
                                    :arc-width="25"
                                    :radius="85"
                                    :central-label-offset-y="5"
                                    :central-label="props.pendingOrders.toString()"
                                    central-sub-label="Ordenes"
                                    :events="pendingEvents"
                                />
                            </ChartContainer>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Inline Records Table Section -->
            <div v-if="activeFilter" class="bg-muted/50 rounded-xl p-1 md:p-4 mt-2">
                <Card class="border-2 border-primary/10">
                    <CardHeader class="flex flex-row items-start justify-between pb-4 space-y-0">
                        <div class="flex flex-col gap-1">
                            <CardTitle class="text-xl font-bold flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full animate-pulse" :class="getIndicatorColor(activeFilter)"></span>
                                Mostrando {{ selectedTitle }}
                            </CardTitle>
                            <CardDescription>
                                {{ isLoading ? 'Obteniendo datos de órdenes...' : `Se encontraron ${orders.length} registros correspondientes al indicador seleccionado para ${currentYear}.` }}
                            </CardDescription>
                        </div>
                        <button 
                            @click.stop="closeTable" 
                            class="p-2 hover:bg-muted rounded-full transition-colors text-muted-foreground hover:text-foreground cursor-pointer"
                            aria-label="Cerrar tabla"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </CardHeader>

                    <CardContent class="pb-6">
                        <!-- Loading State -->
                        <div v-if="isLoading" class="flex flex-col items-center justify-center py-16 gap-3 text-muted-foreground">
                            <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm font-medium">Cargando registros...</span>
                        </div>

                        <!-- Table Data -->
                        <div v-else class="overflow-x-auto border rounded-lg bg-background">
                            <table class="min-w-full divide-y divide-border text-sm">
                                <thead class="bg-muted/40">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold">Orden de Compra</th>
                                        <th class="px-4 py-3 text-left font-semibold">No. Económico</th>
                                        <th class="px-4 py-3 text-left font-semibold">Dependencia</th>
                                        <th class="px-4 py-3 text-left font-semibold">Vehículo</th>
                                        <th class="px-4 py-3 text-left font-semibold">Fecha Solicitud</th>
                                        <th class="px-4 py-3 text-left font-semibold">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    <tr 
                                        v-for="order in orders" 
                                        :key="order.id" 
                                        class="hover:bg-muted/10 transition-colors"
                                    >
                                        <td class="px-4 py-3 font-medium">{{ order.purchase_order }}</td>
                                        <td class="px-4 py-3 text-muted-foreground font-mono">{{ order.economic_number }}</td>
                                        <td class="px-4 py-3">{{ order.dependency?.name || 'N/D' }}</td>
                                        <td class="px-4 py-3 text-muted-foreground">{{ order.vehicle_description || 'N/D' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(order.service_requested_date) }}</td>
                                        <td class="px-4 py-3">
                                            <span 
                                                class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset" 
                                                :class="getStatusBadgeClass(order.status)"
                                            >
                                                {{ getStatusLabel(order.status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="orders.length === 0">
                                        <td colspan="6" class="px-4 py-12 text-center text-muted-foreground">
                                            No se encontraron registros de órdenes para esta selección.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Small Bottom Filler -->
            <div class="h-4" />
        </div>

    </UserLayout>
</template>
