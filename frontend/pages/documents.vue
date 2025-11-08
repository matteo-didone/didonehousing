<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">{{ translations.title }}</h1>
      </div>
      <Button @click="showUploadModal = true" size="lg">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
          <polyline points="17 8 12 3 7 8" />
          <line x1="12" x2="12" y1="3" y2="15" />
        </svg>
        {{ translations.uploadDocument }}
      </Button>
    </div>

    <!-- Filter -->
    <div class="flex gap-3">
      <Select v-model="filterType" class="w-[200px]">
        <option value="">{{ translations.allTypes }}</option>
        <option value="lease">{{ translations.types.lease }}</option>
        <option value="contract">{{ translations.types.contract }}</option>
        <option value="invoice">{{ translations.types.invoice }}</option>
        <option value="receipt">{{ translations.types.receipt }}</option>
        <option value="identity">{{ translations.types.identity }}</option>
        <option value="other">{{ translations.types.other }}</option>
      </Select>
    </div>

    <!-- Documents Table -->
    <Card v-if="filteredDocuments.length > 0">
      <CardContent class="p-0">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="border-b border-border bg-muted/50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                  {{ translations.fileName }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                  {{ translations.type }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                  {{ translations.size }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                  {{ translations.uploadedAt }}
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <tr
                v-for="doc in filteredDocuments"
                :key="doc.id"
                class="hover:bg-muted/50 transition-colors"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                        <polyline points="14 2 14 8 20 8" />
                      </svg>
                    </div>
                    <div>
                      <p class="font-medium">{{ doc.fileName }}</p>
                      <p class="text-xs text-muted-foreground">{{ doc.uploadedBy }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <Badge variant="outline">{{ translations.types[doc.type] }}</Badge>
                </td>
                <td class="px-6 py-4 text-sm text-muted-foreground">
                  {{ doc.size }}
                </td>
                <td class="px-6 py-4 text-sm text-muted-foreground">
                  {{ doc.uploadedAt }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-end gap-2">
                    <Button size="sm" variant="ghost">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="7 10 12 15 17 10" />
                        <line x1="12" x2="12" y1="15" y2="3" />
                      </svg>
                    </Button>
                    <Button size="sm" variant="ghost" class="text-destructive hover:text-destructive">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18" />
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                      </svg>
                    </Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </CardContent>
    </Card>

    <!-- Empty State -->
    <Card v-else>
      <CardContent class="py-12 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-muted mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
            <polyline points="14 2 14 8 20 8" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold">{{ translations.noDocuments }}</h3>
        <p class="mt-2 text-sm text-muted-foreground">{{ translations.noDocumentsDesc }}</p>
        <Button @click="showUploadModal = true" class="mt-4">
          {{ translations.uploadDocument }}
        </Button>
      </CardContent>
    </Card>

    <!-- Upload Modal -->
    <div
      v-if="showUploadModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
      @click.self="showUploadModal = false"
    >
      <Card class="w-full max-w-lg">
        <CardHeader>
          <CardTitle>{{ translations.uploadDocument }}</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="handleUpload" class="space-y-4">
            <!-- File Upload Area -->
            <div class="border-2 border-dashed border-border rounded-lg p-8 text-center hover:border-primary/50 transition-colors cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-muted-foreground mb-4">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <polyline points="17 8 12 3 7 8" />
                <line x1="12" x2="12" y1="3" y2="15" />
              </svg>
              <p class="text-sm text-muted-foreground">{{ translations.dragDrop }}</p>
            </div>

            <!-- Document Type -->
            <div class="space-y-2">
              <Label for="docType">{{ translations.type }}</Label>
              <Select id="docType" v-model="uploadForm.type" required>
                <option value="lease">{{ translations.types.lease }}</option>
                <option value="contract">{{ translations.types.contract }}</option>
                <option value="invoice">{{ translations.types.invoice }}</option>
                <option value="receipt">{{ translations.types.receipt }}</option>
                <option value="identity">{{ translations.types.identity }}</option>
                <option value="other">{{ translations.types.other }}</option>
              </Select>
            </div>

            <div class="flex gap-3 justify-end">
              <Button type="button" variant="outline" @click="showUploadModal = false">
                {{ t('common.cancel') }}
              </Button>
              <Button type="submit" :disabled="uploading">
                <span v-if="uploading">{{ translations.uploading }}</span>
                <span v-else>{{ t('common.upload') }}</span>
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import Card from '@/components/ui/Card.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardContent from '@/components/ui/CardContent.vue'
import Button from '@/components/ui/Button.vue'
import Label from '@/components/ui/Label.vue'
import Select from '@/components/ui/Select.vue'
import Badge from '@/components/ui/Badge.vue'

definePageMeta({
  layout: 'default',
  middleware: 'auth',
})

const { t } = useI18n()

// Translations
const translations = ref({
  title: '',
  uploadDocument: '',
  fileName: '',
  type: '',
  size: '',
  uploadedAt: '',
  download: '',
  delete: '',
  noDocuments: '',
  noDocumentsDesc: '',
  dragDrop: '',
  uploading: '',
  allTypes: '',
  types: { lease: '', contract: '', invoice: '', receipt: '', identity: '', other: '' },
})

onMounted(() => {
  translations.value = {
    title: t('documents.title'),
    uploadDocument: t('documents.uploadDocument'),
    fileName: t('documents.fileName'),
    type: t('documents.type'),
    size: t('documents.size'),
    uploadedAt: t('documents.uploadedAt'),
    download: t('documents.download'),
    delete: t('documents.delete'),
    noDocuments: t('documents.noDocuments'),
    noDocumentsDesc: t('documents.noDocumentsDesc'),
    dragDrop: t('documents.dragDrop'),
    uploading: t('documents.uploading'),
    allTypes: t('documents.allTypes'),
    types: {
      lease: t('documents.types.lease'),
      contract: t('documents.types.contract'),
      invoice: t('documents.types.invoice'),
      receipt: t('documents.types.receipt'),
      identity: t('documents.types.identity'),
      other: t('documents.types.other'),
    },
  }
})

// State
const showUploadModal = ref(false)
const uploading = ref(false)
const filterType = ref('')

const uploadForm = ref({
  type: 'other',
})

// Mock data
const documents = ref([
  {
    id: 1,
    fileName: 'Lease_Agreement_2024.pdf',
    type: 'lease',
    size: '2.4 MB',
    uploadedBy: 'John Doe',
    uploadedAt: '2024-01-15',
  },
  {
    id: 2,
    fileName: 'Rent_Receipt_January.pdf',
    type: 'receipt',
    size: '156 KB',
    uploadedBy: 'System',
    uploadedAt: '2024-01-01',
  },
  {
    id: 3,
    fileName: 'Passport_Copy.pdf',
    type: 'identity',
    size: '1.2 MB',
    uploadedBy: 'John Doe',
    uploadedAt: '2023-12-10',
  },
])

// Computed
const filteredDocuments = computed(() => {
  if (!filterType.value) return documents.value
  return documents.value.filter(d => d.type === filterType.value)
})

// Methods
const handleUpload = async () => {
  uploading.value = true
  try {
    await new Promise((resolve) => setTimeout(resolve, 1000))

    // Add new document
    documents.value.unshift({
      id: documents.value.length + 1,
      fileName: 'New_Document.pdf',
      type: uploadForm.value.type,
      size: '500 KB',
      uploadedBy: 'Current User',
      uploadedAt: new Date().toISOString().split('T')[0],
    })

    showUploadModal.value = false
    uploadForm.value.type = 'other'
    alert('Document uploaded successfully')
  } catch (error) {
    console.error('Error uploading document:', error)
  } finally {
    uploading.value = false
  }
}
</script>
