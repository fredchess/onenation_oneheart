<?php

namespace App\Exports;

use App\Models\Orphanage;
use Maatwebsite\Excel\Concerns\FromCollection;

class OrphanagesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Orphanage::all();
    }
}
