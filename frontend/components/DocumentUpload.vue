<template>
  <div class="document-upload">
    <div class="upload-header">
      <h3>{{ $t('property.documents.title') }}</h3>
      <p class="text-sm text-gray-600">{{ $t('property.documents.subtitle') }}</p>
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
        accept=".pdf,.doc,.docx,image/jpeg,image/png"
        class="hidden"
        @change="handleFileSelect"
      />

      <div class="upload-content" @click="$refs.fileInput.click()">
        <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="upload-text">{{ $t('property.documents.dragDrop') }}</p>
        <p class="upload-hint">{{ $t('property.documents.uploadHint') }}</p>
      </div>
    </div>

    <!-- Document List -->
    <div v-if="documents.length > 0" class="document-list">
      <TransitionGroup name="doc-list">
        <div
          v-for="(doc, index) in documents"
          :key="doc.id || index"
          class="document-item"
        >
          <!-- Document Icon & Info -->
          <div class="document-info">
            <div class="document-icon">
              <svg v-if="doc.type === 'cadastral_survey'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              <svg v-else-if="doc.type === 'energy_certificate'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
            </div>

            <div class="document-details">
              <p class="document-name">{{ doc.file?.name || doc.file_name }}</p>
              <p class="document-size">{{ formatFileSize(doc.file?.size || doc.file_size) }}</p>
            </div>

            <button
              type="button"
              class="delete-btn"
              @click="removeDocument(index)"
              :disabled="uploading"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>

          <!-- Document Type Selector -->
          <div class="document-type">
            <label :for="`doc-type-${index}`" class="type-label">
              {{ $t('property.documents.type') }}
            </label>
            <select
              :id="`doc-type-${index}`"
              v-model="doc.type"
              class="type-select"
            >
              <option value="cadastral_survey">{{ $t('property.documents.types.cadastralSurvey') }}</option>
              <option value="energy_certificate">{{ $t('property.documents.types.energyCertificate') }}</option>
              <option value="proof_of_ownership">{{ $t('property.documents.types.proofOfOwnership') }}</option>
              <option value="other">{{ $t('property.documents.types.other') }}</option>
            </select>
          </div>

          <!-- Description Input -->
          <div class="document-description">
            <label :for="`doc-desc-${index}`" class="description-label">
              {{ $t('property.documents.description') }}
            </label>
            <input
              :id="`doc-desc-${index}`"
              v-model="doc.description"
              type="text"
              :placeholder="$t('property.documents.descriptionPlaceholder')"
              class="description-input"
              maxlength="500"
            />
          </div>

          <!-- Upload Progress -->
          <div v-if="doc.uploading" class="upload-progress">
            <div class="progress-bar">
              <div class="progress-fill" :style="{ width: `${doc.progress || 0}%` }"></div>
            </div>
            <p class="progress-text">{{ doc.progress || 0 }}%</p>
          </div>

          <!-- Upload Status -->
          <div v-else-if="doc.error" class="upload-error">
            <span class="error-text">{{ doc.error }}</span>
          </div>
          <div v-else-if="doc.uploaded" class="upload-success">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="success-text">{{ $t('property.documents.uploaded') }}</span>
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
  maxDocuments: {
    type: Number,
    default: 10
  },
  maxFileSize: {
    type: Number,
    default: 10 * 1024 * 1024 // 10MB
  }
})

const emit = defineEmits(['update:modelValue'])

const fileInput = ref(null)
const isDragging = ref(false)
const uploading = ref(false)

const documents = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const handleFileSelect = (event) => {
  const files = Array.from(event.target.files)
  addFiles(files)
  event.target.value = ''
}

const handleDrop = (event) => {
  isDragging.value = false
  const files = Array.from(event.dataTransfer.files)
  addFiles(files)
}

const addFiles = (files) => {
  if (documents.value.length + files.length > props.maxDocuments) {
    alert(`Maximum ${props.maxDocuments} documents allowed`)
    return
  }

  files.forEach(file => {
    // Validate file size
    if (file.size > props.maxFileSize) {
      alert(`File ${file.name} is too large. Max size is ${props.maxFileSize / 1024 / 1024}MB`)
      return
    }

    // Validate file type
    const allowedTypes = [
      'application/pdf',
      'application/msword',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'image/jpeg',
      'image/png'
    ]

    if (!allowedTypes.includes(file.type)) {
      alert(`File ${file.name} has an invalid type. Only PDF, DOC, DOCX, JPEG, and PNG files are allowed.`)
      return
    }

    documents.value.push({
      id: Date.now() + Math.random(),
      file,
      type: 'other', // Default type
      description: '',
      uploading: false,
      uploaded: false,
      progress: 0,
      error: null
    })
  })
}

const removeDocument = (index) => {
  documents.value.splice(index, 1)
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}

// Expose methods for parent component
defineExpose({
  getDocuments: () => documents.value,
  clearDocuments: () => { documents.value = [] }
})
</script>

<style scoped>
.document-upload {
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

.document-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.document-item {
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1rem;
  background-color: white;
}

.document-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.document-icon {
  flex-shrink: 0;
  width: 2.5rem;
  height: 2.5rem;
  background-color: #eff6ff;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #3b82f6;
}

.document-details {
  flex: 1;
  min-width: 0;
}

.document-name {
  font-size: 0.875rem;
  font-weight: 500;
  color: #1f2937;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.document-size {
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: 0.125rem;
}

.delete-btn {
  flex-shrink: 0;
  color: #ef4444;
  background: none;
  border: none;
  padding: 0.5rem;
  cursor: pointer;
  border-radius: 0.375rem;
  transition: background-color 0.2s;
}

.delete-btn:hover {
  background-color: #fee2e2;
}

.delete-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.document-type {
  margin-bottom: 0.75rem;
}

.type-label,
.description-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.25rem;
}

.type-select,
.description-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  transition: border-color 0.2s;
}

.type-select:focus,
.description-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.document-description {
  margin-bottom: 0.75rem;
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
.doc-list-enter-active,
.doc-list-leave-active {
  transition: all 0.3s ease;
}

.doc-list-enter-from {
  opacity: 0;
  transform: translateX(-20px);
}

.doc-list-leave-to {
  opacity: 0;
  transform: translateX(20px);
}
</style>
