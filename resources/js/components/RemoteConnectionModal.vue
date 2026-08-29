<script setup lang="ts">
import { 
    Smartphone, 
    Wifi, 
    Globe, 
    Copy, 
    Check, 
    X, 
    QrCode,
    RefreshCw,
    CheckCircle2,
    AlertCircle
} from '@lucide/vue';
import QRCode from 'qrcode';
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';

const props = defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const activeTab = ref<'local' | 'remote'>('local');
const canvasRef = ref<HTMLCanvasElement | null>(null);
const copied = ref(false);

// Obtener la IP o hostname actual del navegador
const localIp = ref(window.location.hostname);
const localPort = ref(window.location.port || '8000');
const localUrl = computed(() => `http://${localIp.value}:${localPort.value}`);

// Estado del túnel automático
const tunnelActive = ref(false);
const autoRemoteUrl = ref('');
const isCheckingTunnel = ref(false);
let pollInterval: any = null;

const currentUrl = computed(() => {
    if (activeTab.value === 'local') {
        return localUrl.value;
    }

    return autoRemoteUrl.value || 'https://tu-enlace.trycloudflare.com';
});

const checkTunnelStatus = async () => {
    isCheckingTunnel.value = true;

    try {
        const res = await fetch('/api/tunnel-status');

        if (res.ok) {
            const data = await res.json();

            if (data && data.active && data.url) {
                tunnelActive.value = true;
                autoRemoteUrl.value = data.url;

                if (activeTab.value === 'remote') {
                    generateQr();
                }
            } else {
                tunnelActive.value = false;
                autoRemoteUrl.value = '';
            }
        }
    } catch (e) {
        tunnelActive.value = false;
    } finally {
        isCheckingTunnel.value = false;
    }
};

const generateQr = async () => {
    await nextTick();

    if (!canvasRef.value) {
return;
}

    try {
        await QRCode.toCanvas(canvasRef.value, currentUrl.value, {
            width: 220,
            margin: 2,
            color: {
                dark: '#0f172a',
                light: '#ffffff',
            },
        });
    } catch (err) {
        console.error('Error al generar código QR:', err);
    }
};

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(currentUrl.value);
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    } catch (err) {
        console.error('Error al copiar:', err);
    }
};

watch(() => props.open, (newVal) => {
    if (newVal) {
        checkTunnelStatus();
        setTimeout(generateQr, 100);
        pollInterval = setInterval(checkTunnelStatus, 3000);
    } else {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }
});

watch(activeTab, () => {
    generateQr();
});

onMounted(() => {
    if (props.open) {
        checkTunnelStatus();
        generateQr();
        pollInterval = setInterval(checkTunnelStatus, 3000);
    }
});

onUnmounted(() => {
    if (pollInterval) {
        clearInterval(pollInterval);
    }
});
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 animate-in fade-in duration-150">
        <div class="w-full max-w-md rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-2xl space-y-5">
            
            <!-- Encabezado del Modal -->
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-4">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400">
                        <Smartphone class="h-5 w-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-zinc-950 dark:text-zinc-50">Conexión Remota</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Vincular dispositivo móvil o tablet</p>
                    </div>
                </div>
                <button @click="emit('update:open', false)" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                    <X class="h-4 w-4" />
                </button>
            </div>

            <!-- Selector de Modo de Conexión -->
            <div class="grid grid-cols-2 p-1 bg-zinc-100 dark:bg-zinc-800/80 rounded-xl text-xs font-semibold">
                <button
                    @click="activeTab = 'local'"
                    class="py-2 rounded-lg flex items-center justify-center gap-2 transition-all cursor-pointer"
                    :class="activeTab === 'local' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'"
                >
                    <Wifi class="h-4 w-4" />
                    Red Local (Wi-Fi)
                </button>
                <button
                    @click="activeTab = 'remote'"
                    class="py-2 rounded-lg flex items-center justify-center gap-2 transition-all cursor-pointer relative"
                    :class="activeTab === 'remote' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'"
                >
                    <Globe class="h-4 w-4" />
                    Túnel Remoto
                    <span v-if="tunnelActive" class="absolute top-1.5 right-2 h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                </button>
            </div>

            <!-- Estado del Túnel Remoto si está en Pestaña Remota -->
            <div v-if="activeTab === 'remote'" class="rounded-xl p-3 text-xs" :class="tunnelActive ? 'bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 text-emerald-800 dark:text-emerald-300' : 'bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/40 text-amber-800 dark:text-amber-300'">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <CheckCircle2 v-if="tunnelActive" class="h-4 w-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
                        <AlertCircle v-else class="h-4 w-4 text-amber-600 dark:text-amber-400 shrink-0" />
                        <span class="font-bold">{{ tunnelActive ? 'Túnel Cloudflare Activo' : 'Túnel no iniciado' }}</span>
                    </div>
                    <button @click="checkTunnelStatus" class="p-1 rounded hover:bg-black/5 dark:hover:bg-white/10 transition-colors" title="Actualizar estado">
                        <RefreshCw class="h-3.5 w-3.5" :class="{ 'animate-spin': isCheckingTunnel }" />
                    </button>
                </div>
                <p v-if="!tunnelActive" class="mt-1 text-[11px] text-amber-700 dark:text-amber-400">
                    Ejecuta <code class="font-mono bg-amber-100 dark:bg-amber-900/40 px-1 py-0.5 rounded">npm run tunnel</code> en tu terminal para generar la conexión remota automática.
                </p>
            </div>

            <!-- Contenedor del Código QR -->
            <div class="flex flex-col items-center justify-center p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-100 dark:border-zinc-800/60">
                <div class="p-3 bg-white rounded-xl shadow-sm border border-zinc-200/80">
                    <canvas ref="canvasRef" class="w-48 h-48 block"></canvas>
                </div>
                <span class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-3 font-medium text-center">
                    {{ activeTab === 'local' ? 'Escanea desde la App Android conectada al mismo Wi-Fi' : (tunnelActive ? 'Escanea para acceder desde cualquier lugar con datos móviles' : 'Inicia el túnel para habilitar el acceso remoto') }}
                </span>
            </div>

            <!-- Caja de Dirección y Botón Copiar -->
            <div class="space-y-1.5" v-if="activeTab === 'local' || tunnelActive">
                <div class="flex items-center justify-between text-xs font-semibold text-zinc-600 dark:text-zinc-400">
                    <span>Dirección de Enlace</span>
                    <span v-if="activeTab === 'local'" class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-bold">● Mismo Wi-Fi</span>
                    <span v-else class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-bold">● Acceso Seguro SSL</span>
                </div>
                <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 font-mono text-xs text-zinc-800 dark:text-zinc-200 select-all break-all border border-zinc-200/60 dark:border-zinc-700/60">
                    <span class="truncate">{{ currentUrl }}</span>
                    <button @click="copyToClipboard" class="p-1.5 rounded-lg bg-white dark:bg-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-600 text-zinc-600 dark:text-zinc-200 transition-colors shrink-0 cursor-pointer" title="Copiar dirección">
                        <Check v-if="copied" class="h-3.5 w-3.5 text-emerald-600" />
                        <Copy v-else class="h-3.5 w-3.5" />
                    </button>
                </div>
            </div>

            <!-- Botón Cerrar -->
            <div class="pt-2">
                <button @click="emit('update:open', false)" class="w-full py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-xs font-bold transition-colors cursor-pointer">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</template>
