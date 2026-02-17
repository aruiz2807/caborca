<script setup>
import UserLayout from "@Layouts/UserLayout.vue"
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card'
import { ChartContainer } from "@/Components/ui/chart";
import { VisDonut } from "@unovis/vue"

// Pie chart data
const data = [
  { label: "Orders", value: 100 },
]

// Pie chart data
const data_attended = [
  { label: "Attended", value: 20 },
  { label: "Orders", value: 80 },
]

// Pie chart data
const data_pending = [
  { label: "Pending", value: 80 },
  { label: "Orders", value: 20 },
]

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
                            <CardDescription>Ordenes de 2026</CardDescription>
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
                                    central-label="10"
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
                            <CardDescription>Ordenes de 2026</CardDescription>
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
                                    central-label="8"
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
                            <CardDescription>Ordenes de 2026</CardDescription>
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
                                    central-label="2"
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



