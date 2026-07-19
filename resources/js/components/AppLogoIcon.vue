<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 32 32"
        fill="none"
        :class="className"
        v-bind="$attrs"
    >
        <!-- Fondo base con gradiente e inicio opaco -->
        <path d="M16 2L3 9.5v13L16 30l13-7.5v-13L16 2z" fill="url(#cubeBg)" opacity="0.15" />
        
        <!-- Caras del cubo isométrico con gradientes púrpura contables -->
        <path d="M16 2L3 9.5L16 17l13-7.5L16 2z" fill="url(#cubeTop)" />
        <path d="M3 9.5v13L16 30V17L3 9.5z" fill="url(#cubeLeft)" />
        <path d="M29 9.5v13L16 30V17l13-7.5z" fill="url(#cubeRight)" />
        
        <!-- Línea de tendencia financiera (Crecimiento contable) -->
        <path d="M10 16.5l4 2.5 8-5" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
        
        <defs>
            <linearGradient id="cubeBg" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#c084fc" />
                <stop offset="100%" stop-color="#6366f1" />
            </linearGradient>
            <linearGradient id="cubeTop" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#e9d5ff" />
                <stop offset="100%" stop-color="#a855f7" />
            </linearGradient>
            <linearGradient id="cubeLeft" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#9333ea" />
                <stop offset="100%" stop-color="#7e22ce" />
            </linearGradient>
            <linearGradient id="cubeRight" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#7e22ce" />
                <stop offset="100%" stop-color="#581c87" />
            </linearGradient>
        </defs>
    </svg>
</template>
