<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
            'title'         => [
                'id' => $this->title_id,
                'en' => $this->title_en,
            ],
            'category'      => [
                'id' => $this->category_id,
                'en' => $this->category_en,
            ],
            'content'       => $this->content,
            'author_name'   => $this->author_name ?: optional($this->author)->name,
            'cover_url'     => $this->image ? $this->media_url : null,
            'is_published'  => $this->is_published,
            'published_at'  => $this->published_at?->toIso8601String(),
            'created_at'    => $this->created_at->toIso8601String(),
            'updated_at'    => $this->updated_at->toIso8601String(),
        ];
    }
}
