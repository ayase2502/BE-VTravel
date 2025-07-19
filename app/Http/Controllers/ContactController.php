<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Lấy danh sách liên hệ (Admin)
     */
    public function index(Request $request)
    {
        try {
            $query = Contact::active();

            // Filter theo status
            if ($request->has('status') && in_array($request->status, ['new', 'processed'])) {
                $query->where('status', $request->status);
            }

            // Search theo name, email
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Sắp xếp
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $contacts = $query->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $contacts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách liên hệ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gửi liên hệ (Public)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:100',
                'message' => 'required|string|max:1000'
            ], [
                'name.required' => 'Vui lòng nhập họ tên',
                'name.max' => 'Họ tên không được vượt quá 100 ký tự',
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Email không đúng định dạng',
                'email.max' => 'Email không được vượt quá 100 ký tự',
                'message.required' => 'Vui lòng nhập nội dung',
                'message.max' => 'Nội dung không được vượt quá 1000 ký tự'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $contact = Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
                'status' => 'new',
                'is_deleted' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Gửi liên hệ thành công! Chúng tôi sẽ phản hồi sớm nhất.',
                'data' => $contact
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể gửi liên hệ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xem chi tiết liên hệ (Admin)
     */
    public function show($id)
    {
        try {
            $contact = Contact::active()->where('contact_id', $id)->first();

            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy liên hệ'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $contact
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin liên hệ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật trạng thái liên hệ (Admin)
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:new,processed'
            ], [
                'status.required' => 'Vui lòng chọn trạng thái',
                'status.in' => 'Trạng thái không hợp lệ'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $contact = Contact::active()->where('contact_id', $id)->first();

            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy liên hệ'
                ], 404);
            }

            $contact->update([
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $contact->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa mềm liên hệ (Admin)
     */
    public function softDelete($id)
    {
        try {
            $contact = Contact::active()->where('contact_id', $id)->first();

            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy liên hệ'
                ], 404);
            }

            $contact->update(['is_deleted' => 'inactive']);

            return response()->json([
                'success' => true,
                'message' => 'Xóa liên hệ thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa liên hệ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách liên hệ đã xóa (Admin)
     */
    public function trashed(Request $request)
    {
        try {
            $contacts = Contact::inactive()
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $contacts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách liên hệ đã xóa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Khôi phục liên hệ (Admin)
     */
    public function restore($id)
    {
        try {
            $contact = Contact::inactive()->where('contact_id', $id)->first();

            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy liên hệ đã xóa'
                ], 404);
            }

            $contact->update(['is_deleted' => 'active']);

            return response()->json([
                'success' => true,
                'message' => 'Khôi phục liên hệ thành công',
                'data' => $contact->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể khôi phục liên hệ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa vĩnh viễn liên hệ (Admin)
     */
    public function destroy($id)
    {
        try {
            $contact = Contact::where('contact_id', $id)->first();

            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy liên hệ'
                ], 404);
            }

            $contact->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa vĩnh viễn liên hệ thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa vĩnh viễn liên hệ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thống kê liên hệ (Admin)
     */
    public function statistics()
    {
        try {
            $stats = [
                'total_contacts' => Contact::active()->count(),
                'new_contacts' => Contact::active()->where('status', 'new')->count(),
                'processed_contacts' => Contact::active()->where('status', 'processed')->count(),
                'deleted_contacts' => Contact::where('is_deleted', 'inactive')->count(),
                'today_contacts' => Contact::active()->whereDate('created_at', today())->count(),
                'this_month_contacts' => Contact::active()->whereMonth('created_at', now()->month)->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thống kê',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
