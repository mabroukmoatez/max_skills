<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    /**
     * Upload files with progress tracking
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files.*' => 'required|file|mimes:jpeg,jpg,png,gif,webp,pdf|max:2048', // 2MB max
            'payment_method' => 'required|string|in:virement_bancaire,d17,espece'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $uploadedFiles = [];
        $paymentMethod = $request->input('payment_method');

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                try {
                    // Generate unique filename
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $filename = Str::uuid() . '_' . time() . '.' . $extension;
                    
                    // Store file in payment method specific directory
                    $path = $file->storeAs("uploads/{$paymentMethod}", $filename, 'public');
                    
                    $uploadedFiles[] = [
                        'id' => Str::uuid(),
                        'original_name' => $originalName,
                        'filename' => $filename,
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'url' => Storage::url($path)
                    ];
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreur lors de l\'upload du fichier: ' . $originalName,
                        'error' => $e->getMessage()
                    ], 500);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Fichiers uploadés avec succès',
            'files' => $uploadedFiles
        ]);
    }

    /**
     * Delete uploaded file
     */
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_path' => 'required|string',
            'file_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $filePath = $request->input('file_path');
        $fileId = $request->input('file_id');

        try {
            // Remove 'storage/' prefix if present
            $cleanPath = str_replace('storage/', '', $filePath);
            
            if (Storage::disk('public')->exists($cleanPath)) {
                Storage::disk('public')->delete($cleanPath);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Fichier supprimé avec succès',
                    'file_id' => $fileId
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non trouvé'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du fichier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upload progress (for chunked uploads if needed)
     */
    public function getProgress(Request $request)
    {
        $uploadId = $request->input('upload_id');
        
        // This would typically check a cache or database for upload progress
        // For now, we'll return a simple response
        return response()->json([
            'success' => true,
            'progress' => 100, // Percentage
            'upload_id' => $uploadId
        ]);
    }

    /**
     * Validate file before upload (optional pre-check)
     */
    public function validateFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_name' => 'required|string',
            'file_size' => 'required|integer|max:2097152', // 2MB in bytes
            'file_type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $fileName = $request->input('file_name');
        $fileSize = $request->input('file_size');
        $fileType = $request->input('file_type');

        // Check file extension
        $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp', 'pdf'];
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            return response()->json([
                'success' => false,
                'message' => 'Type de fichier non autorisé. Formats acceptés: JPEG, PNG, GIF, WebP, PDF'
            ], 422);
        }

        // Check file size (2MB max)
        if ($fileSize > 2097152) {
            return response()->json([
                'success' => false,
                'message' => 'Fichier trop volumineux. Taille maximale: 2 Mo'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fichier valide',
            'file_info' => [
                'name' => $fileName,
                'size' => $fileSize,
                'type' => $fileType,
                'extension' => $extension
            ]
        ]);
    }
}

