<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Monica API",
 *     version="0.1"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get current user data",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="The user data"
     *     )
     * )
     */
    public function __invoke(Request $request)
    {
        return $request->user();
    }
}
