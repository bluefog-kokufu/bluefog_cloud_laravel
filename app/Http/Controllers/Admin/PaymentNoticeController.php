<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentNotice\StorePaymentNoticeRequest;
use App\Http\Requests\PaymentNotice\UpdatePaymentNoticeRequest;
use App\Models\Company;
use App\Models\Customer;
use App\Models\PaymentNotice;
use App\Services\PaymentNoticeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentNoticeController extends Controller
{
    public function __construct(private readonly PaymentNoticeService $paymentNoticeService) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['from', 'to']);
        $paymentNotices = $this->paymentNoticeService->paginate($filters);

        return view('admin.paynotices.index', compact('paymentNotices', 'filters'));
    }

    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();
        $company = Company::query()->first();
        $noticeNo = $this->paymentNoticeService->previewNoticeNo();

        return view('admin.paynotices.form', ['paymentNotice' => null, 'customers' => $customers, 'company' => $company, 'noticeNo' => $noticeNo]);
    }

    /**
     * 「番号を採番し直す」操作用に、次の通知番号候補を返す
     */
    public function nextNumber(Request $request): JsonResponse
    {
        $bump = max(0, (int) $request->query('bump', 0));

        return response()->json(['id' => $this->paymentNoticeService->previewNoticeNo($bump)]);
    }

    public function store(StorePaymentNoticeRequest $request): RedirectResponse
    {
        $this->paymentNoticeService->create($request->validated());

        return redirect()->route('paynotice')->with('status', '支払通知書を作成しました。');
    }

    public function edit(PaymentNotice $paynotice): View
    {
        $customers = Customer::orderBy('name')->get();
        $company = Company::query()->first();

        return view('admin.paynotices.form', ['paymentNotice' => $paynotice, 'customers' => $customers, 'company' => $company]);
    }

    public function update(UpdatePaymentNoticeRequest $request, PaymentNotice $paynotice): RedirectResponse
    {
        $this->paymentNoticeService->update($paynotice, $request->validated());

        return redirect()->route('paynotice')->with('status', '支払通知書を更新しました。');
    }

    public function destroy(PaymentNotice $paynotice): JsonResponse
    {
        $this->paymentNoticeService->delete($paynotice);

        return response()->json(['status' => 'deleted']);
    }

    public function show(PaymentNotice $paynotice): string
    {
        $paynotice->loadMissing('customer');
        $company = Company::query()->first();
        $totals = $this->paymentNoticeService->totals($paynotice->items ?? []);

        return view('admin.paynotices.show', [
            'paymentNotice' => $paynotice,
            'customer' => $paynotice->customer,
            'company' => $company,
            'totals' => $totals,
        ])->render();
    }
}
