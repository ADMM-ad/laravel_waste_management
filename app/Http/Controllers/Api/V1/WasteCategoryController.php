<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\WasteCategory;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class WasteCategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * List all waste categories.
     */
    public function index(): JsonResponse
    {
        $categories = WasteCategory::select('id', 'name', 'description', 'created_at')->get();
        return $this->jsonResponse(true, 'Daftar kategori sampah berhasil diambil.', $categories);
    }
}
