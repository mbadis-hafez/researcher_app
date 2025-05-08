<?php

namespace App\Voyager\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Database\Schema\SchemaManager;

class ArtworksResearcherWidget extends AbstractWidget
{
    protected $config = [];

    public function shouldBeDisplayed()
    {
        return Auth::check() && Auth::user()->role->name === 'researcher';
    }

    public function run()
    {
        $dataType = Voyager::model('DataType')->where('slug', 'artworks')->firstOrFail();

        $getter = $dataType->server_side ? 'paginate' : 'get';
        $orderBy = request()->get('order_by') ?? ($dataType->order_column ?? 'id');
        $sortOrder = request()->get('sort_order') ?? ($dataType->order_direction ?? 'desc');
        $search = request()->get('s', '');
        $usesSoftDeletes = false;
        $showSoftDeleted = false;

        $relationships = $this->getRelationships($dataType);

        $modelClass = $dataType->model_name;
        $modelInstance = app($modelClass);

        // Select only the required columns: image, title, year, and location
        $query = $modelInstance->newQuery()->select(['id', 'image', 'title', 'year', 'location'])->with($relationships);

        // Apply search filter (basic full-table search)
        if ($search) {
            $columns = SchemaManager::describeTable($modelInstance->getTable());
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $column) {
                    $q->orWhere($column['Field'], 'like', "%{$search}%");
                }
            });
        }

        if ($orderBy) {
            $query->orderBy($orderBy, $sortOrder ?? 'asc');
        }

        $dataTypeContent = $query->{$getter}();

        // Filter the browseRows to show only the selected columns
        $dataType->browseRows = $dataType->browseRows->filter(function ($row) {
            return in_array($row->field, ['image', 'title', 'year', 'location']);
        });

        return view('voyager::bread.browse', [
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent,
            'isModelTranslatable' => false,
            'search' => $search,
            'orderBy' => $orderBy,
            'sortOrder' => $sortOrder,
            'orderColumn' => '',
            'searchable' => SchemaManager::describeTable($modelInstance->getTable())->pluck('Field')->toArray(),
            'isServerSide' => $dataType->server_side,
            'defaultSearchKey' => $dataType->default_search_key ?? null,
            'usesSoftDeletes' => $usesSoftDeletes,
            'showSoftDeleted' => $showSoftDeleted,
            'showCheckboxColumn' => false,
            'showSortOrder' => false,
            'actions' => [], // No action buttons
            'widget' => true,
        ]);
    }

    protected function getRelationships($dataType)
    {
        $relationships = [];
        foreach ($dataType->browseRows as $row) {
            $options = is_string($row->details) ? json_decode($row->details) : $row->details;
            if (isset($options->relationship)) {
                $relationships[] = $row->field;
            }
        }
        return $relationships;
    }
}
