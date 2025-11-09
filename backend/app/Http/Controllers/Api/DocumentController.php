<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents for a property
     */
    public function index(Request $request)
    {
        $request->validate([
            'documentable_type' => 'sometimes|string',
            'documentable_id' => 'sometimes|integer',
            'type' => 'sometimes|string',
        ]);

        $query = Document::query();

        if ($request->has('documentable_type') && $request->has('documentable_id')) {
            $query->where('documentable_type', $request->documentable_type)
                  ->where('documentable_id', $request->documentable_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $documents = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $documents,
        ]);
    }

    /**
     * Store a newly uploaded document
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|integer',
            'type' => [
                'required',
                'string',
                Rule::in([
                    'letter_of_intent',
                    'rental_agreement',
                    'proof_of_ownership',
                    'paint_refund',
                    'inventory_form',
                    'utilities_form',
                    'photo',
                    'cadastral_survey', // visura catastale
                    'energy_certificate', // APE
                    'other'
                ])
            ],
            'description' => 'nullable|string|max:500',
            'locale' => 'sometimes|string|in:en,it',
        ]);

        // Verify that the documentable exists
        $documentableClass = 'App\\Models\\' . $validated['documentable_type'];
        if (!class_exists($documentableClass)) {
            return response()->json([
                'message' => 'Invalid documentable type'
            ], 400);
        }

        $documentable = $documentableClass::find($validated['documentable_id']);
        if (!$documentable) {
            return response()->json([
                'message' => 'Documentable resource not found'
            ], 404);
        }

        // Check authorization - user must own the resource
        if (method_exists($documentable, 'landlord') && $documentable->landlord_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Validate file type based on document type
        if ($validated['type'] === 'photo') {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            if (!in_array($mimeType, $allowedMimes)) {
                return response()->json([
                    'message' => 'Photos must be JPEG, PNG, or WebP format'
                ], 422);
            }
        } else {
            // For documents, allow common document formats
            $allowedMimes = [
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/jpg',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            if (!in_array($mimeType, $allowedMimes)) {
                return response()->json([
                    'message' => 'Documents must be PDF, JPEG, PNG, DOC, or DOCX format'
                ], 422);
            }
        }

        // Generate unique filename
        $filename = Str::uuid() . '.' . $extension;
        $path = "documents/{$validated['documentable_type']}/{$validated['documentable_id']}/{$filename}";

        // Store file
        Storage::disk('minio')->put($path, file_get_contents($file), 'private');

        // Create document record
        $document = new Document([
            'documentable_type' => $documentableClass,
            'documentable_id' => $validated['documentable_id'],
            'type' => $validated['type'],
            'file_path' => $path,
            'file_name' => $originalName,
            'mime_type' => $mimeType,
            'file_size' => $size,
            'locale' => $validated['locale'] ?? 'en',
            'status' => 'draft',
            'metadata' => [
                'description' => $validated['description'] ?? null,
                'uploaded_by' => auth()->id(),
            ],
        ]);

        $document->save();

        return response()->json([
            'message' => 'Document uploaded successfully',
            'data' => $document,
        ], 201);
    }

    /**
     * Display the specified document
     */
    public function show(Document $document)
    {
        // Check authorization
        $documentable = $document->documentable;
        if (method_exists($documentable, 'landlord') && $documentable->landlord_id !== auth()->id()) {
            // Allow HO to view as well
            if (!auth()->user()->hasRole('housing_office')) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        return response()->json([
            'data' => $document,
        ]);
    }

    /**
     * Download the document file
     */
    public function download(Document $document)
    {
        // Check authorization
        $documentable = $document->documentable;
        if (method_exists($documentable, 'landlord') && $documentable->landlord_id !== auth()->id()) {
            // Allow HO to download as well
            if (!auth()->user()->hasRole('housing_office')) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        if (!Storage::disk('minio')->exists($document->file_path)) {
            return response()->json([
                'message' => 'File not found'
            ], 404);
        }

        return Storage::disk('minio')->download($document->file_path, $document->file_name);
    }

    /**
     * Update the specified document metadata
     */
    public function update(Request $request, Document $document)
    {
        // Check authorization
        $documentable = $document->documentable;
        if (method_exists($documentable, 'landlord') && $documentable->landlord_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'description' => 'sometimes|string|max:500',
            'type' => [
                'sometimes',
                'string',
                Rule::in([
                    'letter_of_intent',
                    'rental_agreement',
                    'proof_of_ownership',
                    'paint_refund',
                    'inventory_form',
                    'utilities_form',
                    'photo',
                    'cadastral_survey',
                    'energy_certificate',
                    'other'
                ])
            ],
        ]);

        if (isset($validated['description'])) {
            $metadata = $document->metadata ?? [];
            $metadata['description'] = $validated['description'];
            $document->metadata = $metadata;
        }

        if (isset($validated['type'])) {
            $document->type = $validated['type'];
        }

        $document->save();

        return response()->json([
            'message' => 'Document updated successfully',
            'data' => $document,
        ]);
    }

    /**
     * Remove the specified document
     */
    public function destroy(Document $document)
    {
        // Check authorization
        $documentable = $document->documentable;
        if (method_exists($documentable, 'landlord') && $documentable->landlord_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // Delete file from storage
        if (Storage::disk('minio')->exists($document->file_path)) {
            Storage::disk('minio')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'message' => 'Document deleted successfully',
        ]);
    }
}
