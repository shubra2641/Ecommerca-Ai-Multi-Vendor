<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SimpleChartsController extends Controller
{
    /**
     * Display simple charts page
     */
    public function index()
    {
        return view('admin.simple-charts');
    }

    /**
     * Get sales data for charts
     */
    public function getSalesData()
    {
        // Simple sales data - replace with your actual data
        $data = [
            'labels' => ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            'values' => [120, 190, 300, 500, 200, 300],
            'title' => 'المبيعات الشهرية',
            'color' => '#007bff',
        ];

        return response()->json($data);
    }

    /**
     * Get users data for charts
     */
    public function getUsersData()
    {
        // Simple users data - replace with your actual data
        $data = [
            'labels' => ['نشط', 'معلق', 'معطل'],
            'values' => [65, 25, 10],
            'colors' => ['#28a745', '#ffc107', '#dc3545'],
        ];

        return response()->json($data);
    }

    /**
     * Get orders data for charts
     */
    public function getOrdersData()
    {
        // Simple orders data - replace with your actual data
        $data = [
            'labels' => ['مكتمل', 'قيد المراجعة', 'ملغي', 'معلق'],
            'values' => [45, 30, 15, 10],
            'colors' => ['#28a745', '#007bff', '#dc3545', '#ffc107'],
        ];

        return response()->json($data);
    }
}
