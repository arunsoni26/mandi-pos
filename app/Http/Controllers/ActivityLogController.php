<?php

namespace App\Http\Controllers;

use App\Models\CreditorInvoice;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = ActivityLog::with('user')
                ->latest();

            // Filter by date
            if ($request->date) {
                $query->whereDate('created_at', $request->date);
            }

            // Filter by user
            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by action
            if ($request->action) {
                $query->where('action', $request->action);
            }

            $logs = $query->get();

            $data = $logs->map(function ($row) {
                $formatValues = function ($values) {

                    if (!$values || !is_array($values)) {
                        return '-';
                    }

                    $output = '';

                    foreach ($values as $key => $value) {

                        // Make field name human readable
                        $label = ucwords(str_replace('_', ' ', $key));

                        // Format dates
                        if (str_contains($key, 'date') && $value) {
                            $value = Carbon::parse($value)->format('d M Y');
                        }

                        // Format amounts
                        if (str_contains($key, 'total') || str_contains($key, 'amount')) {
                            $value = number_format((float) $value, 2);
                        }

                        $output .= "<div><strong>{$label}:</strong> {$value}</div>";
                    }

                    return $output ?: '-';
                };

                $modelName = class_basename($row->model_type);
                $module = $modelLabels[$modelName] ?? ucwords(str_replace('_', ' ', $modelName));

                $reference = "";
                if ($modelName === 'CreditorInvoice') {
                    $invoice = CreditorInvoice::find($row->model_id);
                    if ($invoice) {
                        $reference = invoiceNumber($invoice); // your helper
                    }
                }

                return [
                    'date' => Carbon::parse($row->created_at)->format('d M Y h:i A'),
                    'user' => $row->user->name ?? 'System',
                    'action' => ucfirst($row->action),
                    'model' => $module,
                    'model_id' => $reference,
                    'old_values' => !empty($row->old_values) ? $row->old_values : "--",
                    'new_values' => !empty($row->new_values) ? $row->new_value : "--",
                ];
            });

            return response()->json(['data' => $data]);
        }

        $users = User::all();

        return view('admin.activity-logs.index', compact('users'));
    }
}
