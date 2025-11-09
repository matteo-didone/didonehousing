<template>
  <div class="photo-upload">
    <div class="upload-header">
      <h3>{{ $t('property.photos.title') }}</h3>
      <p class="text-sm text-gray-600">{{ $t('property.photos.subtitle') }}</p>
    </div>

    <!-- Upload Area -->
    <div
      class="upload-zone"
      :class="{ 'drag-over': isDragging }"
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="handleDrop"
    >
      <input
        ref="fileInput"
        type="file"
        multiple
        accept="image/jpeg,image/png,image/jpg,image/webp"
        class="hidden"
        @change="handleFileSelect"
      />

      <div class="upload-content" @click="$refs.fileInput.click()">
        <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        <p class="upload-text">{{ $t('property.photos.dragDrop') }}</p>
        <p class="upload-hint">{{ $t('property.photos.uploadHint') }}</p>
      </div>
    </div>

    <!-- Photo Gallery -->
    <div v-if="photos.length > 0" class="photo-gallery">
      <TransitionGroup name="photo-list">
        <div
          v-for="(photo, index) in photos"
          :key="photo.id || index"
          class="photo-item"
        >
          <!-- Image Preview -->
          <div class="photo-preview">
            <img :src="photo.preview || photo.url" :alt="photo.description || 'Photo'" />
            <button
              type="button"
              class="delete-btn"
              @click="removePhoto(index)"
              :disabled="uploading"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Description Input -->
          <div class="photo-description">
            <label :for="`photo-desc-${index}`" class="description-label">
              {{ $t('property.photos.description') }}
            </label>
            <input
              :id="`photo-desc-${index}`"
              v-model="photo.description"
              type="text"
              :placeholder="$t('property.photos.descriptionPlaceholder')"
              class="description-input"
              maxlength="500"
            />
          </div>

          <!-- Upload Progress -->
          <div v-if="photo.uploading" class="upload-progress">
            <div class="progress-bar">
              <div class="progress-fill" :style="{ width: `${photo.progress || 0}%` }"></div>
            </div>
            <p class="progress-text">{{ photo.progress || 0 }}%</p>
          </div>

          <!-- Upload Status -->
          <div v-else-if="photo.error" class="upload-error">
            <span class="error-text">{{ photo.error }}</span>
          </div>
          <div v-else-if="photo.uploaded" class="upload-success">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="success-text">{{ $t('property.photos.uploaded') }}</span>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  maxPhotos: {
    type: Number,
    default: 20
  },
  maxFileSize: {
    type: Number,
    default: 5 * 1024 * 1024 // 5MB
  }
})

const emit = defineEmits(['update:modelValue'])

const fileInput = ref(null)
const isDragging = ref(false)
const uploading = ref(false)

const photos = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const handleFileSelect = (event) => {
  const files = Array.from(event.target.files)
  addFiles(files)
  // Reset input so same file can be selected again
  event.target.value = ''
}

const handleDrop = (event) => {
  isDragging.value = false
  const files = Array.from(event.dataTransfer.files).filter(file =>
    file.type.startsWith('image/')
  )
  addFiles(files)
}

const addFiles = (files) => {
  if (photos.value.length + files.length > props.maxPhotos) {
    alert(`Maximum ${props.maxPhotos} photos allowed`)
    return
  }

  files.forEach(file => {
    // Validate file size
    if (file.size > props.maxFileSize) {
      alert(`File ${file.name} is too large. Max size is ${props.maxFileSize / 1024 / 1024}MB`)
      return
    }

    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
      photos.value.push({
        id: Date.now() + Math.random(),
        file,
        preview: e.target.result,
        description: '',
        uploading: false,
        uploaded: false,
        progress: 0,
        error: null
      })
    }
    reader.readAsDataURL(file)
  })
}

const removePhoto = (index) => {
  photos.value.splice(index, 1)
}

// Expose methods for parent component
defineExpose({
  getPhotos: () => photos.value,
  clearPhotos: () => { photos.value = [] }
})
</script>

<style scoped>
.photo-upload {
  width: 100%;
}

.upload-header h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.upload-zone {
  border: 2px dashed #d1d5db;
  border-radius: 0.5rem;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
  background-color: #f9fafb;
  margin-bottom: 1.5rem;
}

.upload-zone:hover,
.upload-zone.drag-over {
  border-color: #3b82f6;
  background-color: #eff6ff;
}

.upload-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.upload-icon {
  width: 3rem;
  height: 3rem;
  color: #9ca3af;
}

.upload-text {
  font-size: 1rem;
  font-weight: 500;
  color: #374151;
}

.upload-hint {
  font-size: 0.875rem;
  color: #6b7280;
}

.photo-gallery {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
}

.photo-item {
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1rem;
  background-color: white;
}

.photo-preview {
  position: relative;
  width: 100%;
  padding-bottom: 75%; /* 4:3 aspect ratio */
  overflow: hidden;
  border-radius: 0.375rem;
  margin-bottom: 0.75rem;
}

.photo-preview img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.delete-btn {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background-color: rgba(239, 68, 68, 0.9);
  color: white;
  border: none;
  border-radius: 0.375rem;
  padding: 0.5rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.delete-btn:hover {
  background-color: rgba(220, 38, 38, 0.9);
}

.delete-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.photo-description {
  margin-bottom: 0.75rem;
}

.description-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.25rem;
}

.description-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  transition: border-color 0.2s;
}

.description-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.upload-progress {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.progress-bar {
  flex: 1;
  height: 0.5rem;
  background-color: #e5e7eb;
  border-radius: 9999px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background-color: #3b82f6;
  transition: width 0.3s;
}

.progress-text {
  font-size: 0.75rem;
  font-weight: 500;
  color: #6b7280;
  min-width: 3rem;
  text-align: right;
}

.upload-error {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #dc2626;
  font-size: 0.875rem;
}

.upload-success {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #059669;
  font-size: 0.875rem;
}

/* Transitions */
.photo-list-enter-active,
.photo-list-leave-active {
  transition: all 0.3s ease;
}

.photo-list-enter-from {
  opacity: 0;
  transform: scale(0.8);
}

.photo-list-leave-to {
  opacity: 0;
  transform: scale(0.8);
}
</style>
