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

                        $label = ucwords(str_replace('_', ' ', $key));

                        // ✅ If value is array → convert nicely
                        if (is_array($value)) {

                            $value = collect($value)->map(function ($item) {
                                if (is_array($item)) {
                                    return implode(', ', $item);
                                }
                                return $item;
                            })->implode(' | ');
                        }

                        // Format dates
                        if (str_contains($key, 'date') && !empty($value)) {
                            try {
                                $value = \Carbon\Carbon::parse($value)->format('d M Y');
                            } catch (\Exception $e) {
                                // skip if invalid date
                            }
                        }

                        // Format money
                        if (str_contains($key, 'total') || str_contains($key, 'amount')) {
                            if (is_numeric($value)) {
                                $value = number_format((float) $value, 2);
                            }
                        }

                        $output .= "<div><strong>{$label}:</strong> {$value}</div>";
                    }

                    return $output ?: '-';
                };

                $modelName = class_basename($row->model_type);
                $module = $modelLabels[$modelName] ?? ucwords(str_replace('_', ' ', $modelName));

                $reference = "";
                if ($modelName === 'CreditorInvoice') {
                    $invoice = CreditorInvoice::withTrashed()->where('id', $row->model_id)->first();
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
                    'old_values' => $formatValues($row->old_values),
                    'new_values' => $formatValues($row->new_values),
                ];
            });

            return response()->json(['data' => $data]);
        }

        $users = User::all();

        return view('admin.activity-logs.index', compact('users'));
    }
}
