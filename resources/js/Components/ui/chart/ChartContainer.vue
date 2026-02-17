<script setup>
import { VisSingleContainer } from "@unovis/vue";
</script>

<template>
  <!-- The fixed sizing wrapper that stops resize loops -->
  <div class="relative w-full h-full min-h-0 overflow-hidden">
    <VisSingleContainer>
      <slot />
    </VisSingleContainer>
  </div>
</template>
