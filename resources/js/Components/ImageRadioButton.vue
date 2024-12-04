<template>
    <div class="image-radio-group">
        <div v-for="(option, index) in options" :key="index" class="image-radio-option"
            :class="{ 'selected': modelValue === option.value }" @click="selectOption(option.value)">
            <img :src="option.image" :alt="option.label" class="image-radio-img">
            <p class="text-left image-radio-label">{{ option.label }}</p>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

// Hapus `modelValue: { required: true }`
const props = defineProps({
    modelValue: [String, Number],
    options: {
        type: Array,
        required: true,
        validator: (value) => value.every(option =>
            option.value !== undefined &&
            option.image !== undefined &&
            option.label !== undefined
        )
    }
})

const emit = defineEmits(['update:modelValue'])

const selectOption = (value) => {
    emit('update:modelValue', value)
}
</script>

<style scoped>
.image-radio-group {
    display: flex;
    gap: 16px;
}

.image-radio-option {
    text-align: left;
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    border: 2px solid transparent;
    border-radius: 8px;
    padding: 10px;
    transition: all 0.3s ease;
}

.image-radio-img:hover {
    border-color: #3498db;
    scale: 1.05;
    animation-duration: 300ms;
    transition-duration: 300ms;
}

.image-radio-option.selected img {
    scale: 1.5;
}

.image-radio-img {
    padding: 40px 20px;
    width: 12vw;
    border-radius: 50%;
    height: fit;
    object-fit: cover;
    border-radius: 4px;
}

.image-radio-label {
    margin-top: 8px;
    font-size: 14px;
    text-align: left !important;
}

.image-radio-desc {
    margin-top: 8px;
    font-size: 14px;
    text-align: left !important;
}
</style>
