@php
    $edit = !is_null($dataTypeContent->getKey());
    $add = is_null($dataTypeContent->getKey());
    $dataTypeRows = $dataType->{$edit ? 'editRows' : 'addRows'};
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.' . ($edit ? 'edit' : 'add')) . ' ' .
    $dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.' . ($edit ? 'edit' : 'add')) . ' ' . $dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form" class="form-edit-add"
                        action="{{ $edit ? route('voyager.' . $dataType->slug . '.update', $dataTypeContent->getKey()) : route('voyager.' . $dataType->slug . '.store') }}"
                        method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if ($edit)
                            {{ method_field('PUT') }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Tabbed Interface -->
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#basic_info" data-toggle="tab">Basic Information</a></li>
                                    <li><a href="#description" data-toggle="tab">Description & Details</a></li>
                                    <li><a href="#sources" data-toggle="tab">Sources</a></li>
                                    <li><a href="#location" data-toggle="tab">Location</a></li>
                                </ul>

                                <div class="tab-content">
                                    <!-- Basic Information Tab -->
                                    <div class="tab-pane active" id="basic_info">
                                        @php
                                            $basicInfoRows = $dataTypeRows->filter(function ($item) {
                                                return in_array($item->field, [
                                                    'image',
                                                    'title',
                                                    'year',
                                                    'medium',
                                                    'dimension',
                                                ]);
                                            });
                                        @endphp

                                        <div class="form-group col-md-12 {{ $errors->has('owner_id') ? 'has-error' : '' }}">
                                            <label class="control-label">Artist</label>
                                            <select class="form-control select2" name="owner_id" id="artist-select">
                                                <option value="">-- Select Artist --</option>
                                                @foreach (App\Artist::orderBy('name')->get() as $artist)
                                                    <option value="{{ $artist->id }}"
                                                        @if ((old('owner_id') ?? ($dataTypeContent->owner_id ?? null)) == $artist->id) selected @endif>
                                                        {{ $artist->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @if ($errors->has('owner_id'))
                                                @foreach ($errors->get('owner_id') as $error)
                                                    <span class="help-block">{{ $error }}</span>
                                                @endforeach
                                            @endif
                                        </div>

                                        @foreach ($basicInfoRows as $row)
                                            @include('voyager::multilingual.input-hidden-bread-edit-add')
                                            <div
                                                class="form-group @if ($row->type == 'hidden') hidden @endif col-md-{{ $row->details->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}">
                                                <label
                                                    class="control-label">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                                {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                                @if ($errors->has($row->field))
                                                    @foreach ($errors->get($row->field) as $error)
                                                        <span class="help-block">{{ $error }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Description & Details Tab -->
                                    <div class="tab-pane" id="description">
                                        @php
                                            $descriptionRows = $dataTypeRows->filter(function ($item) {
                                                return in_array($item->field, [
                                                    'description',
                                                    'additional_information',
                                                    'provenance',
                                                    'exhibitions',
                                                ]);
                                            });
                                        @endphp

                                        @foreach ($descriptionRows as $row)
                                            @include('voyager::multilingual.input-hidden-bread-edit-add')
                                            <div
                                                class="form-group @if ($row->type == 'hidden') hidden @endif col-md-{{ $row->details->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}">
                                                <label
                                                    class="control-label">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                                {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                                @if ($errors->has($row->field))
                                                    @foreach ($errors->get($row->field) as $error)
                                                        <span class="help-block">{{ $error }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Sources Tab -->
                                    <div class="tab-pane" id="sources">
                                        @php
                                            $sourcesRows = $dataTypeRows->filter(function ($item) {
                                                return in_array($item->field, [
                                                    'source_book',
                                                    'source_photo',
                                                    'other_source',
                                                ]);
                                            });
                                        @endphp

                                        @foreach ($sourcesRows as $row)
                                            @include('voyager::multilingual.input-hidden-bread-edit-add')
                                            <div
                                                class="form-group @if ($row->type == 'hidden') hidden @endif col-md-{{ $row->details->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}">
                                                <label
                                                    class="control-label">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                                {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                                @if ($errors->has($row->field))
                                                    @foreach ($errors->get($row->field) as $error)
                                                        <span class="help-block">{{ $error }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Location Tab -->
                                    <div class="tab-pane" id="location">
                                        @php
                                            $locationRows = $dataTypeRows->filter(function ($item) {
                                                return in_array($item->field, ['location', 'latitude', 'longitude']);
                                            });
                                        @endphp

                                        @foreach ($locationRows as $row)
                                            @include('voyager::multilingual.input-hidden-bread-edit-add')
                                            <div
                                                class="form-group @if ($row->type == 'hidden') hidden @endif col-md-{{ $row->details->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}">
                                                <label
                                                    class="control-label">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                                {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                                @if ($errors->has($row->field))
                                                    @foreach ($errors->get($row->field) as $error)
                                                        <span class="help-block">{{ $error }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <!-- End Tabbed Interface -->

                        </div><!-- panel-body -->

                        <div class="panel-footer">
                        @section('submit-buttons')
                            <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                        @stop
                        @yield('submit-buttons')
                    </div>
                </form>

                <div style="display:none">
                    <input type="hidden" id="upload_url" value="{{ route('voyager.upload') }}">
                    <input type="hidden" id="upload_type_slug" value="{{ $dataType->slug }}">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade modal-danger" id="confirm_delete_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
            </div>

            <div class="modal-body">
                <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                <button type="button" class="btn btn-danger"
                    id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script>
    $(document).ready(function() {
        // Initialize select2
        $('#artist-select').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select an artist',
            allowClear: true
        });


        $('#select2-location-05-container').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select a country',
            allowClear: true
        });
        // Make sure select2 is properly destroyed before form submission
        $('form').on('submit', function() {
            $(this).find('.select2-hidden-accessible').select2('destroy');
        });
        // Initialize all the standard Voyager JS
        $('.toggleswitch').bootstrapToggle();

        // Datepicker initialization
        $('.form-group input[type=date]').each(function(idx, elt) {
            if (elt.hasAttribute('data-datepicker')) {
                elt.type = 'text';
                $(elt).datetimepicker($(elt).data('datepicker'));
            } else if (elt.type != 'date') {
                elt.type = 'text';
                $(elt).datetimepicker({
                    format: 'L',
                    extraFormats: ['YYYY-MM-DD']
                }).datetimepicker($(elt).data('datepicker'));
            }
        });

        @if ($isModelTranslatable)
            $('.side-body').multilingual({
                "editing": true
            });
        @endif
    });
</script>

<script>
    var params = {};
    var $file;

    function deleteHandler(tag, isMulti) {
        return function() {
            $file = $(this).siblings(tag);

            params = {
                slug: '{{ $dataType->slug }}',
                filename: $file.data('file-name'),
                id: $file.data('id'),
                field: $file.parent().data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            }

            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
        };
    }

    $('document').ready(function() {
        $('.toggleswitch').bootstrapToggle();

        //Init datepicker for date fields if data-datepicker attribute defined
        //or if browser does not handle date inputs
        $('.form-group input[type=date]').each(function(idx, elt) {
            if (elt.hasAttribute('data-datepicker')) {
                elt.type = 'text';
                $(elt).datetimepicker($(elt).data('datepicker'));
            } else if (elt.type != 'date') {
                elt.type = 'text';
                $(elt).datetimepicker({
                    format: 'L',
                    extraFormats: ['YYYY-MM-DD']
                }).datetimepicker($(elt).data('datepicker'));
            }
        });

        @if ($isModelTranslatable)
            $('.side-body').multilingual({
                "editing": true
            });
        @endif

        $('.side-body input[data-slug-origin]').each(function(i, el) {
            $(el).slugify();
        });

        $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
        $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
        $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
        $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

        $('#confirm_delete').on('click', function() {
            $.post('{{ route('voyager.' . $dataType->slug . '.media.remove') }}', params, function(
                response) {
                if (response &&
                    response.data &&
                    response.data.status &&
                    response.data.status == 200) {

                    toastr.success(response.data.message);
                    $file.parent().fadeOut(300, function() {
                        $(this).remove();
                    })
                } else {
                    toastr.error("Error removing file.");
                }
            });

            $('#confirm_delete_modal').modal('hide');
        });
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@stop
