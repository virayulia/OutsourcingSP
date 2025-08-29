@extends('layouts.main')

@section('title', 'Paket')

@section('content')

<h3 class="mt-4">Update Paket</h3>
<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/update-paket/{{$dataP->paket_id}}">
                            <!-- Input Date Range -->
        @csrf
            <div class="mb-3">
                <label for="paket" class="form-label">Paket</label>
                <input type="text" class="form-control" name="paket" id="paket" value="{{$dataP->paket}}">
            </div>
            <div class="mb-3">
                <label for="kuota_paket" class="form-label">Kuota Paket</label>
                <input type="text" class="form-control" name="kuota_paket" id="kuota_paket" value="{{$dataP->kuota_paket}}">
            </div>
            <div class="mb-3">
                <label class="form-label" for="unit_kerja">Unit kerja</label>
                <select class="custom-select select2" name="unit_kerja" id="unit_kerja">
                    <option selected>Pilih Unit Kerja</option>
                    @foreach ($unit as $item)
                        <option value="{{ $item->unit_id }}" 
                            {{ $item->unit_id == $dataP->unit_id ? 'selected' : '' }}>
                            {{ $item->unit_kerja }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
@endsection
