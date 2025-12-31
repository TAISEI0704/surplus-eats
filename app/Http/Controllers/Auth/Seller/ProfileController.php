<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\SellerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 販売者プロフィール取得Controller
 */
final class ProfileController extends Controller
{
    /**
     * プロフィール取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        /** @var \App\Models\Seller $seller */
        $seller = $request->user();

        return response()->json([
            'seller' => new SellerResource($seller),
        ], 200);
    }
}
