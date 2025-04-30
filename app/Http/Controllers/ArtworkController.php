<?php 

namespace App\Http\Controllers;

use App\Exports\ArtworksExport;
use App\Http\Controllers\Voyager\VoyagerBaseController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use TCG\Voyager\Facades\Voyager;

class ArtworkController extends VoyagerBaseController
{

    public function exportToExcel()
    {
        return Excel::download(new ArtworksExport, 'artworks.xlsx');
    }

    
    public function index(Request $request)
    {
        // Get the DataType
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        
        // For researchers, modify the query to only show their artworks
        if (auth()->user()->hasRole('researcher')) {
            $this->addAuthorFilter($request, $dataType);
        }
        
        return parent::index($request);
    }
    
    protected function addAuthorFilter(Request $request, $dataType)
    {
        // Get the model instance
        $model = app($dataType->model_name);
        
        // Add global scope for this request
        $model::addGlobalScope('researcher_artworks', function ($builder) {
            $builder->where('author_id', auth()->id());
        });
        
        // Alternatively, modify the request to include the filter
        $request->merge([
            'key' => 'author_id',
            'filter' => 'equals',
            's' => auth()->id()
        ]);
    }

   
}