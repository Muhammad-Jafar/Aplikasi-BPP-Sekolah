@extends('layouts.main', ['title' => 'Laporan', 'page_heading' => 'Data laporan'])

@section('content')
<section>
	<div class="row">
		<div class="col-4 col-lg-4 col-md-4">
			<div class="card">
				<div class="card-body px-3 py-4-4">
					<div class="row">
						<div class="col-md-4">
							<div class="stats-icon">
								<i class="iconly-boldChart"></i>
							</div>
						</div>
						<div class="col-md-8">
							<h6 class="text-muted font-semibold">Total Hari Ini</h6>
							<h6 class="font-extrabold mb-0">
								{{ $sum['thisDay'] }}
							</h6>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-4 col-lg-4 col-md-4">
			<div class="card">
				<div class="card-body px-3 py-4-4">
					<div class="row">
						<div class="col-md-4">
							<div class="stats-icon">
								<i class="iconly-boldChart"></i>
							</div>
						</div>
						<div class="col-md-8">
							<h6 class="text-muted font-semibold">Total Bulan Ini</h6>
							<h6 class="font-extrabold mb-0">
								{{ $sum['thisMonth'] }}
							</h6>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-4 col-lg-4 col-md-4">
			<div class="card">
				<div class="card-body px-3 py-4-4">
					<div class="row">
						<div class="col-md-4">
							<div class="stats-icon">
								<i class="iconly-boldChart"></i>
							</div>
						</div>
						<div class="col-md-8">
							<h6 class="text-muted font-semibold">Total Tahun Ini</h6>
							<h6 class="font-extrabold mb-0">
								{{ $sum['thisYear'] }}
							</h6>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="card px-3 py-3">
			<form action="" method="GET">
				<label for="start_date" class="pb-3 fw-bold">Filter Data dengan Rentang Tanggal :</label>
				<div class="input-group">
					<input type="date" name="start_date" class="form-control" placeholder="Pilih tanggal awal..">
					<input type="date" name="end_date" class="form-control" placeholder="Pilih tanggal akhir..">
					<button type="submit" class="btn btn-primary">Filter</button>
				</div>
			</form>
		</div>
	</div>

	@empty(!$filteredResult)
	<div class="row">
		<div class="card px-3 py-3">
			<div class="col-lg-12">
				<a href="{{ route('report.export', [$filteredResult['startDate'], $filteredResult['endDate']]) }}"
					class="btn btn-success float-end">
					<i class="bi bi-file-earmark-excel-fill"></i>
					Export Excel
				</a>
			</div>

			<div class="table-responsive mt-3">
				<table class="table caption-top" id="datatable">
					<caption>Laporan data dari tanggal <span class="fw-bold">{{ $filteredResult['startDate'] }}</span> -
						<span class="fw-bold">{{ $filteredResult['endDate'] }}</span>
					</caption>
					<thead>
						<tr>
							<th scope="col">#</th>
							<th scope="col">Tanggal</th>
							<th scope="col">Nama Siswa</th>
							<th scope="col">NIS</th>
							<th scope="col">Kelas</th>
							<th scope="col">Jurusan</th>
							<th scope="col">Angkatan</th>
							<th scope="col">Telah Lunas</th>
							<th scope="col">Sisa tagihan</th>
							<th scope="col">Status</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($filteredResult['cashTransactions'] as $cashTransaction)
							<tr>
								<th>{{ $loop->iteration }}</th>
								<td>{{ date('d-m-Y', strtotime($cashTransaction->updated_at)) }}</td>
								<td>{{ $cashTransaction->students->name }}</td>
								<td>{{ $cashTransaction->students->student_identification_number }}</td>
								<td>{{ $cashTransaction->students->school_class->name }}</td>
								<td>{{ $cashTransaction->students->school_major->abbreviated_word }}</td>
								<td>{{ $cashTransaction->students->school_year_start }}</td>
								<td>{{ indonesianCurrency($cashTransaction->recent_bill) }}</td>
								<td>{{ indonesianCurrency($cashTransaction->billings - $cashTransaction->recent_bill) }}</td>
								<td>{{ $cashTransaction->status }}</td>
							</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<td colspan="4" align="right"><b>Total</b></td>
							<td>{{ indonesianCurrency($filteredResult['sumOfAmount']) }}</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
	@endempty
</section>
@endsection

@push('js')
@include('reports.script')
@endpush
