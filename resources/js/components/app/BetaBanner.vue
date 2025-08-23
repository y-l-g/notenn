<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { X } from 'lucide-vue-next'
import { ref } from 'vue'
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
const isBannerVisible = ref(true)

function closeBanner() {
    isBannerVisible.value = false
    localStorage.setItem('betaBannerClosed', 'true')
}

if (localStorage.getItem('betaBannerClosed') === 'true') {
    isBannerVisible.value = false
}

</script>

<template>
    <div v-if="isBannerVisible" class="fixed top-0 left-0 right-0 z-50 bg-destructive text-white">
        <div class="mx-auto flex items-center justify-between py-1 px-2">
            <span class="text-sm">
                {{ t('beta') }}
            </span>
            <Button variant="ghost" size="icon" class="text-white cursor-pointer hover:bg-destructive"
                @click="closeBanner">
                <X />
            </Button>
        </div>
    </div>
</template>
