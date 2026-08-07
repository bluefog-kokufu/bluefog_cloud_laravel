<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentNotice\StorePaymentNoticeRequest;
use App\Http\Requests\PaymentNotice\UpdatePaymentNoticeRequest;
use App\Models\Company;
use App\Models\Customer;
use App\Models\PaymentNotice;
use App\Repositories\PaymentNoticeRepositoryInterface;
use App\Services\PaymentNoticeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentNoticeController extends Controller
{
    public function __construct(
        private readonly PaymentNoticeService $paymentNoticeService,
        private readonly PaymentNoticeRepositoryInterface $paymentNotices,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['from', 'to']);
        $paymentNotices = $this->paymentNoticeService->paginate($filters);
        $customers = Customer::orderBy('name')->get();

        // バリデーションエラーで戻ってきた場合、直前に開いていたフォームをモーダルで再表示する
        $reopenPaymentNotice = old('paynotice_id') ? $this->paymentNotices->find(old('paynotice_id')) : null;

        return view('admin.paynotices.index', compact('paymentNotices', 'customers', 'filters', 'reopenPaymentNotice'));
    }

    public function create(): string
    {
        $customers = Customer::orderBy('name')->get();
        $company = Company::query()->first();

        return view('admin.paynotices.form', ['paymentNotice' => null, 'customers' => $customers, 'company' => $company])->render();
    }

    public function store(StorePaymentNoticeRequest $request): RedirectResponse
    {
        $this->paymentNoticeService->create($request->validated());

        return redirect()->route('paynotice')->with('status', '支払通知書を作成しました。');
    }

    public function edit(PaymentNotice $paynotice): string
    {
        $customers = Customer::orderBy('name')->get();
        $company = Company::query()->first();

        return view('admin.paynotices.form', ['paymentNotice' => $paynotice, 'customers' => $customers, 'company' => $company])->render();
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
