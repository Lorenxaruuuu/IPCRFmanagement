<?php
// app/Http/Controllers/FormController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormController extends Controller
{
    /**
     * Return a collection of sample forms. No database or table access is
     * performed; the application works entirely from this static list.
     */
    protected function sampleForms()
    {
        return collect([
            (object)[ 'id' => 1, 'name' => 'Purchase Request Form', 'file' => 'forms/purchase_request.pdf' ],
            (object)[ 'id' => 2, 'name' => 'Travel Order Form', 'file' => 'forms/travel_order.pdf' ],
            (object)[ 'id' => 3, 'name' => 'Leave Application Form', 'file' => 'forms/leave_application.pdf' ],
            (object)[ 'id' => 4, 'name' => 'Overtime Request Form', 'file' => 'forms/overtime_request.pdf' ],
            (object)[ 'id' => 5, 'name' => 'Cash Advance Form', 'file' => 'forms/cash_advance.pdf' ],
            (object)[ 'id' => 6, 'name' => 'Liquidation Report Form', 'file' => 'forms/liquidation.pdf' ],
            (object)[ 'id' => 7, 'name' => 'Property Acknowledgement Form', 'file' => 'forms/property_ack.pdf' ],
            (object)[ 'id' => 8, 'name' => 'Requisition Slip Form', 'file' => 'forms/requisition.pdf' ],
        ]);
    }

    public function index()
    {
        $forms = $this->sampleForms();
        return view('form', compact('forms'));
    }

    public function download($id)
    {
        $form = $this->sampleForms()->firstWhere('id', $id);

        if (! $form) {
            abort(404);
        }

        return response()->download(storage_path('app/public/' . $form->file));
    }
}