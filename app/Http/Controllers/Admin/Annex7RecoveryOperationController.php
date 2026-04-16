<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Annex7RecoveryOperation;
use Illuminate\Http\Request;

class Annex7RecoveryOperationController extends Controller
{
    public function index()
    {
        $operations = Annex7RecoveryOperation::orderBy('code')->paginate(20);

        return view('admin.annex7.recovery_operations.index', compact('operations'));
    }

    public function create()
    {
        return view('admin.annex7.recovery_operations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:annex7_recovery_operations,code',
            'description' => 'required|string|max:500',
        ]);

        Annex7RecoveryOperation::create($request->only('code', 'description'));

        return redirect()->route('admin.annex7-recovery-operations.index')
            ->with('success', 'Operacja odzysku została dodana.');
    }

    public function edit(Annex7RecoveryOperation $annex7RecoveryOperation)
    {
        return view('admin.annex7.recovery_operations.edit', compact('annex7RecoveryOperation'));
    }

    public function update(Request $request, Annex7RecoveryOperation $annex7RecoveryOperation)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:annex7_recovery_operations,code,'.$annex7RecoveryOperation->id,
            'description' => 'required|string|max:500',
        ]);

        $annex7RecoveryOperation->update($request->only('code', 'description'));

        return redirect()->route('admin.annex7-recovery-operations.index')
            ->with('success', 'Operacja odzysku została zaktualizowana.');
    }

    public function destroy(Annex7RecoveryOperation $annex7RecoveryOperation)
    {
        if ($annex7RecoveryOperation->shipments()->exists()) {
            return back()->with('error', 'Nie można usunąć operacji powiązanej z dokumentami Annex 7.');
        }

        $annex7RecoveryOperation->delete();

        return redirect()->route('admin.annex7-recovery-operations.index')
            ->with('success', 'Operacja odzysku została usunięta.');
    }
}
