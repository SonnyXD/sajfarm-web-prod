<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
//use \Mpdf\Mpdf as PDF; 
use Codedge\Fpdf\Fpdf\Fpdf;
use PDF;
use Auth;

use \App\Models\Invoice;
use \App\Models\Institution;
use \App\Models\Provider;

class GeneratePDFController extends Controller
{
    // public function document()
    // {
    //     // Setup a filename 
    //     $documentFileName = "fun.pdf";
 
    //     // Create the mPDF document
    //     $document = new PDF( [
    //         'mode' => 'utf-8',
    //         'format' => 'A4',
    //         'margin_header' => '3',
    //         'margin_top' => '20',
    //         'margin_bottom' => '20',
    //         'margin_footer' => '2',
    //     ]);     
 
    //     // Set some header informations for output
    //     $header = [
    //         'Content-Type' => 'application/pdf',
    //         'Content-Disposition' => 'inline; filename="'.$documentFileName.'"'
    //     ];
 
    //     // Write some simple Content
    //     $document->WriteHTML('<h1 style="color:blue">TheCodingJack</h1>');
    //     $document->WriteHTML('<p>Write something, just for fun!</p>');
         
    //     // Save PDF on your public storage 
    //     Storage::disk('public')->put($documentFileName, $document->Output($documentFileName, "S"));
         
    //     // Get file back from storage with the give header informations
    //     return Storage::disk('public')->download($documentFileName, 'Request', $header); //
    // }

    // protected $fpdf;
 
    // public function __construct()
    // {
    //     $this->fpdf = new Fpdf;
    // }

    // public function index() 
    // {
    // 	$this->fpdf->SetFont('Arial', 'B', 15);
    //     $this->fpdf->AddPage("L", ['100', '100']);
    //     $this->fpdf->Text(10, 10, "Hello World!");
    //     $this->fpdf->Text(12, 13, "Hello World!");  
    //     $this->fpdf->Text(10, 10, "Hello World!");  
    //     $this->fpdf->Text(10, 10, "Hello World!");  
    //     $this->fpdf->Text(10, 10, "Hello World!");  
    //     $this->fpdf->Text(10, 10, "Hello World!");  
    //     $this->fpdf->Text(10, 10, "Hello World!");  
    //     $this->fpdf->Text(10, 10, "Hello World!");  
    //     $this->fpdf->Text(10, 10, "Hello World!");  
    //     $this->fpdf->Text(10, 10, "Hello World!");  

         
    //     $this->fpdf->Output();

    //     exit;
    // }

    public function invoice(Request $request)
    {
        $invoices = Invoice::all();
        $invoice_id = $invoices->last()->id;
        $user = Auth::user();

        $date = $request->input('document-date');
        $provider_id = $request->input('furnizor-select');
        $provider = Provider::where('id', $provider_id)->get();
        $invoice_number = $request->input('document-number');
        $due_date = $request->input('due-date');

        $institution = Institution::all();

        $filename = 'nir'.$invoice_id.'.pdf';

        $html = '<html>
                <head>
                <style>
                td, th {border: 2px solid black;}
                </style>
                </head>
                ';

        $html .= ' <span style="font-weight: bold; float: left;">'. $institution[0]->name .'</span>
                <br>
                <span style="float: left;">Utilizator: '. $user->name .'</span>
                <h2 style="font-weight:bold; text-align: center;">NOTA DE INTRARE RECEPTIE</h2>
                <br>
                <span style="font-weight: bold; float: right;">Numar document: '. $invoice_id . ' / ' . $date .'</span>
                <br>
                <span style="font-weight: bold; float: right;">Furnizor: '. $provider .'</span>
                <br>
                <span style="font-weight: bold; float: right;">Gestiune: DEPOZIT FARMACIE</span>
                <br>
                <span style="font-weight: bold; float: right;">Document intrare: Factura fiscala - '. $invoice_number .'</span>
                <br>
                <span style="font-weight: bold; float: right;">Data scadenta: '. $due_date .'</span>
                <br>
                <br>
                <br>
        ';

        $html .= '
        <table>
        <tr>
          <th style="font-weight: bold; text-align: center;">Cod CIM</th>
          <th style="font-weight: bold; text-align: center;">Cod Produs</th>
          <th style="font-weight: bold; text-align: center;">Lot</th>
          <th style="font-weight: bold; text-align: center;">Data Exp.</th>
          <th style="font-weight: bold; text-align: center;">UM</th>
          <th style="font-weight: bold; text-align: center;">Cantitate</th>
          <th style="font-weight: bold; text-align: center;">Pret Unitar</th>
          <th style="font-weight: bold; text-align: center;">Pret cu TVA</th>
          <th style="font-weight: bold; text-align: center;">Valoare (RON)</th>
        </tr>
        ';

        $html .= '';

        $html .= '</table>';

        $html .= '</html>';
        
        PDF::SetTitle('NIR');
        PDF::AddPage('L', 'A4');
        PDF::writeHTML($html, true, false, true, false, '');

        PDF::Output(public_path($filename), 'F');

        return response()->download(public_path($filename));
    }

}
