<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ConferenceDocItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_id',
        'type',
        'content',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'uploaded_by',
        'is_public',
    ];

    protected $casts = [
        'content' => 'array',
        'is_public' => 'boolean',
        'file_size' => 'integer',
    ];

    // Relationships
    public function conferenceDoc()
    {
        return $this->belongsTo(ConferenceDoc::class, 'doc_id');
    }

    // Legacy relationship for backward compatibility
    public function conferenceKit()
    {
        return $this->conferenceDoc();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // File-related methods
    public function getFileUrl()
    {
        if ($this->file_path && $this->is_public) {
            return Storage::disk('public')->url($this->file_path);
        }
        return null;
    }

    public function getFileSizeFormatted()
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImage()
    {
        return $this->mime_type && str_starts_with($this->mime_type, 'image/');
    }

    public function isDocument()
    {
        $documentMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'application/rtf',
        ];
        
        return $this->mime_type && in_array($this->mime_type, $documentMimes);
    }

    public function isVideo()
    {
        return $this->mime_type && str_starts_with($this->mime_type, 'video/');
    }

    public function isAudio()
    {
        return $this->mime_type && str_starts_with($this->mime_type, 'audio/');
    }

    public function deleteFile()
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            Storage::disk('public')->delete($this->file_path);
        }
    }

    // Override delete to also delete the file
    public function delete()
    {
        $this->deleteFile();
        return parent::delete();
    }
}
