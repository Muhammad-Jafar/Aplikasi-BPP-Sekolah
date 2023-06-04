<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\CashTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Repositories\CashTransactionRepository;
use App\Http\Requests\CashTransactionStoreRequest;
use App\Http\Requests\CashTransactionUpdateRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CashTransactionController extends Controller
{
    private $cashTransactionRepository, $startOfWeek, $endOfWeek;

    public function __construct(CashTransactionRepository $cashTransactionRepository)
    {
        $this->cashTransactionRepository = $cashTransactionRepository;
        $this->startOfWeek = now()->startOfWeek()->format('Y-m-d');
        $this->endOfWeek = now()->endOfWeek()->format('Y-m-d');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(): View|JsonResponse
    {
        $cashTransactions = CashTransaction::with('students:id,name')
            ->select('id', 'transaction_code', 'student_id', 'amount', 'paid_on', 'is_paid', 'note')
            // ->whereBetween('paid_on', [$this->startOfWeek, $this->endOfWeek])
            // ->latest()
            ->get();

        $students = Student::select('id', 'student_identification_number', 'name')
            ->whereDoesntHave(
                'cash_transactions',
                fn (Builder $query) => $query->select(['paid_on'])
                    ->whereBetween('paid_on', [$this->startOfWeek, $this->endOfWeek])
            )->get();

        if (request()->ajax()) {
            return datatables()->of($cashTransactions)
                ->addIndexColumn()
                ->addColumn('amount', fn ($model) => indonesianCurrency($model->amount))
                ->addColumn('paid_on', fn ($model) => date('d-m-Y', strtotime($model->date)))
                ->addColumn('is_paid', 'cash_transactions.datatable.status')
                ->rawColumns(['is_paid'])
                ->toJson();
        }

        $cashTransactionTrashedCount = CashTransaction::onlyTrashed()->count();

        return view('cash_transactions.index', [
            'students' => $students,
            'data' => $this->cashTransactionRepository->results(),
            'cashTransactionTrashedCount' => $cashTransactionTrashedCount
        ]);
    }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\CashTransactionStoreRequest  $request
    //  * @return \Illuminate\Http\RedirectResponse
    //  */
    // public function store(CashTransactionStoreRequest $request): RedirectResponse
    // {
    //     foreach ($request->student_id as $student_id) {
    //         Auth::user()->cash_transactions()->create([
    //             'student_id' => $student_id,
    //             'amount' => $request->amount,
    //             'paid_on' => $request->paid_on,
    //             'note' => $request->note
    //         ]);
    //     }

        // return redirect()->route('cash-transactions.index')->with('success', 'Data berhasil ditambahkan!');
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\CashTransactionUpdateRequest  $request
    //  * @param  \App\Models\CashTransaction  $cashTransaction
    //  * @return \Illuminate\Http\RedirectResponse
    //  */
    // public function update(CashTransactionUpdateRequest $request, CashTransaction $cashTransaction): RedirectResponse
    // {
    //     $cashTransaction->update($request->validated());

    //     return redirect()->route('cash-transactions.index')->with('success', 'Data berhasil diubah!');
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  \App\Models\CashTransaction  $cashTransaction
    //  * @return \Illuminate\Http\RedirectResponse
    //  */
    // public function destroy(CashTransaction $cashTransaction): RedirectResponse
    // {
    //     $cashTransaction->delete();

    //     return redirect()->route('cash-transactions.index')->with('success', 'Data berhasil dihapus!');
    // }
}
