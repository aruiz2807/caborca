<script setup>
import UserLayout from "@Layouts/UserLayout.vue"
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card'
import { ChartContainer } from "@/Components/ui/chart";
import { VisDonut } from "@unovis/vue"
import { computed } from 'vue'

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
</script>

<template>
    <UserLayout tabTitle="Home" appName="Application">

        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="bg-muted/50 aspect-video rounded-xl">
                    <Card class="flex flex-col">
                        <CardHeader class="items-center pb-0">
                            <CardTitle>Indicador de ordenes solicitadas</CardTitle>
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
                                />
                            </ChartContainer>
                        </CardContent>
                    </Card>
                </div>

                <div class="bg-muted/50 aspect-video rounded-xl">
                    <Card class="flex flex-col">
                        <CardHeader class="items-center pb-0">
                            <CardTitle>Indicador de ordenes atendidas</CardTitle>
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
                                />
                            </ChartContainer>
                        </CardContent>
                    </Card>
                </div>
                <div class="bg-muted/50 aspect-video rounded-xl">
                    <Card class="flex flex-col">
                        <CardHeader class="items-center pb-0">
                            <CardTitle>Indicador de ordenes pendientes</CardTitle>
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
                                />
                            </ChartContainer>
                        </CardContent>
                    </Card>
                </div>
            </div>
            <div class="bg-muted/50 min-h-[100vh] flex-1 rounded-xl md:min-h-min" />
        </div>

    </UserLayout>
</template>



