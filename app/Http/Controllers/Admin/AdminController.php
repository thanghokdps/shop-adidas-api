<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;

class AdminController
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index(): JsonResponse
    {
        return ResponseHelper::send($this->adminService->all());
    }
}
