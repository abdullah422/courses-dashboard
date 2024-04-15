@extends('layouts.admin.app')

@section('content')

    <div>
        <h2>@lang('courses.courses')</h2>
    </div>

    <ul class="breadcrumb mt-2">
        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">@lang('site.home')</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">@lang('courses.courses')</a></li>
        <li class="breadcrumb-item">@lang('site.edit')</li>
    </ul>

    <div class="row">

        <div class="col-md-12">

            <div class="tile shadow">

                <form method="post" action="{{ route('admin.courses.update', $course->id) }}">
                    @csrf
                    @method('put')

                    @include('admin.partials._errors')
                    <div class="col-md-6">
                        {{--name--}}
                        <div class="form-group">
                            <label>@lang('courses.tittle') <span class="text-danger">*</span></label>
                            <input type="text" name="tittle" class="form-control" value="{{ old('tittle', $course->tittle) }}" required>
                        </div>
                    </div><!-- end of col -->


                    <div class="col-md-6">


                        {{--stage--}}
                        <div class="form-group">
                            <label>@lang('stages.stage_withal')<span class="text-danger">*</span></label>
                            <select name="stage_id" class="form-control">
                                <option value="{{$course->stage->id}}">{{$course->stage->name}}</option>
                                @foreach($stages as $stage)
                                    <option value="{{$stage->id}}">{{$stage->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        {{--Button--}}
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i>@lang('site.update')</button>
                        </div>

                    </div><!-- end of col -->






                </form><!-- end of form -->

            </div><!-- end of tile -->

        </div><!-- end of col -->

    </div><!-- end of row -->

@endsection

