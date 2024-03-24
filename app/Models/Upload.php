<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Upload extends Model
{
    const DEFAULT_DISK = 'upload';

    const UPLOAD_TYPE_IMAGE = 'image';
    const UPLOAD_TYPE_FILE = 'file';

    protected $fillable = [
        'name',
        'disk',
        'path',
        'type',
        'extension'
    ];

    protected $appends = [
        'url',
        'source'
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleted(function ($model) {
            $disk = Storage::disk($model->disk);
            $disk->delete($this->path);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function source() :Attribute
    {
        return Attribute::make(function () {
            return $this->id;
        });
    }

    protected function fullPath() :Attribute
    {
        return Attribute::make(function () {
            return Storage::disk($this->disk)->path($this->path);
        });
    }

    protected function url() :Attribute
    {
        return Attribute::make(function () {
            return Storage::disk($this->disk)->url($this->path);
        });
    }

    public function upload($file)
    {
        $disk = Storage::disk($this->disk);
        $fileName = Str::random(40) . '.' . $this->extension;
        $path = $disk->putFileAs(Str::plural($this->type), $file, $fileName);
        $this->path = $path;
        $this->user()->associate(Auth::user());
        $this->save();
    }

    public function download()
    {
        $disk = Storage::disk($this->disk);
        return $disk->download($this->path);
    }
}
